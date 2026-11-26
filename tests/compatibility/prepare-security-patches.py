"""Apply pinned Adobe CE patches to a clean, disposable Magento 2.4.9 source checkout.

This prepares source-level compatibility tests, not a deployed Magento installation.
No credentials, store configuration, database or production keys are used or changed.
"""

import argparse
import hashlib
import io
import json
import re
import subprocess
import urllib.request
import zipfile
from pathlib import Path


SOURCE_COMMIT = "755e34dd689021c5165db9d35ecff74f7dc51527"
PATCHES = [
    ("2-4-9-jul-2026.zip", "650e8e34e925653a1b3980ce053b9cb993dc2b22e72ee80b5557e03f36848984", "249-2026-07-001-CE.patch"),
    ("2-4-9-aug-2026.zip", "0f300d043b5f7f9558d2d1ffe13b91ec84b5766549adbaec1fc73278ff72b0e1", "249-2026-08-001-CE.patch"),
    ("VULN-39341-composer-patches.zip", "5f98d409e627c0ac77fb9cbff4bf139c17ad69550dc06411a4b4d0aeadd669e1", "VULN-39341_249.patch"),
    ("2-4-9-sep-2026.zip", "9fe17a9681d94deaaeb2ca9245e1e1bcd9fad9b3478fbcaaa8ab1a94ee4cd686", "249-2026-09-001-CE.patch"),
    ("2-4-9-oct-2026.zip", "1d995f5998a65dc46c76c31bb4cff799ebaf26121ca933ec18a708393b2f1d30", "249-2026-10-001-CE.patch"),
]


def git(source, *arguments, input_text=None):
    result = subprocess.run(
        ["git", "-C", str(source), *arguments],
        input=input_text, capture_output=True, text=True, encoding="utf-8", check=True,
    )
    return result.stdout.strip()


def source_path(path, packages):
    if path.startswith("vendor/magento/"):
        _, vendor, package, relative = path.split("/", 3)
        return packages[vendor + "/" + package] + "/" + relative
    return path


def remap_patch(text, packages):
    lines = []
    for line in text.splitlines(keepends=True):
        if line.startswith("diff --git "):
            match = re.fullmatch(r"diff --git a/(.*?) b/(.*?)\n?", line)
            if not match:
                raise ValueError("Unexpected patch header: " + line)
            line = "diff --git a/" + source_path(match[1], packages) + " b/" + source_path(match[2], packages) + "\n"
        elif line.startswith("--- a/") or line.startswith("+++ b/"):
            line = line[:6] + source_path(line[6:].rstrip("\n"), packages) + "\n"
        lines.append(line)
    return "".join(lines)


def main():
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("source", type=Path)
    parser.add_argument("--report", type=Path, required=True)
    args = parser.parse_args()
    source = args.source.resolve()
    metadata = json.loads((source / "composer.json").read_text(encoding="utf-8"))
    if metadata.get("name") != "magento/magento2ce" or metadata.get("version") != "2.4.9":
        raise ValueError("Only an original Magento Open Source 2.4.9 checkout is supported.")
    if git(source, "rev-parse", "HEAD") != SOURCE_COMMIT or git(source, "status", "--porcelain"):
        raise ValueError("Use a clean, disposable checkout of the pinned 2.4.9 commit.")

    packages = {"magento/framework": "lib/internal/Magento/Framework"}
    for manifest in (source / "app/code/Magento").glob("*/composer.json"):
        package = json.loads(manifest.read_text(encoding="utf-8"))
        packages[package["name"]] = manifest.parent.relative_to(source).as_posix()

    report = {"source_commit": SOURCE_COMMIT, "patches": []}
    for archive_name, expected_sha, patch_name in PATCHES:
        url = "https://repo.magento.com/patch/" + archive_name
        with urllib.request.urlopen(url, timeout=60) as response:
            archive = response.read()
        if hashlib.sha256(archive).hexdigest() != expected_sha:
            raise ValueError("Archive checksum changed: " + url)
        with zipfile.ZipFile(io.BytesIO(archive)) as zipped:
            candidates = [name for name in zipped.namelist()
                          if not name.startswith("__MACOSX/") and Path(name).name == patch_name]
            if len(candidates) != 1:
                raise ValueError("Cannot identify patch: " + patch_name)
            patch = zipped.read(candidates[0])
        mapped = remap_patch(patch.decode("utf-8"), packages)
        # Ignore whitespace only to accommodate CRLF in original upstream blobs.
        git(source, "apply", "--ignore-whitespace", "--check", "-", input_text=mapped)
        git(source, "apply", "--ignore-whitespace", "--whitespace=nowarn", "-", input_text=mapped)
        git(source, "apply", "--ignore-whitespace", "--reverse", "--check", "-", input_text=mapped)
        report["patches"].append({"url": url, "archive_sha256": expected_sha,
                                   "patch": patch_name, "patch_sha256": hashlib.sha256(patch).hexdigest()})
        print("Applied and reverse-checked: " + patch_name, flush=True)

    args.report.parent.mkdir(parents=True, exist_ok=True)
    args.report.write_text(json.dumps(report, indent=2) + "\n", encoding="utf-8")


if __name__ == "__main__":
    main()
