import os
import platform
import subprocess
import sys
import shutil

INSTALLERS_DIR = os.path.join(os.path.dirname(__file__), "installers")
BUILD_TOOLS_PATH = os.path.join(INSTALLERS_DIR, "vs_BuildTools.exe")
REQUIREMENTS_PATH = "requirements.txt"

def run_command(cmd, shell=False):
    """Run a shell command and raise on failure."""
    print(f"\n>>> Running: {' '.join(cmd) if isinstance(cmd, list) else cmd}")
    try:
        subprocess.check_call(cmd, shell=shell)
    except subprocess.CalledProcessError as e:
        raise RuntimeError(f"Command failed with exit code {e.returncode}: {e}") from e

def add_to_path(path):
    """Add a directory to the User PATH permanently."""
    if not os.path.exists(path):
        raise FileNotFoundError(f"Path not found, cannot add to PATH: {path}")
    print(f"Adding to PATH: {path}")
    run_command([
        "setx", "PATH", f"%PATH%;{path}"
    ], shell=True)
    print("Path added successfully...")

def install_build_tools_windows():
    """Check and install Visual Studio Build Tools if needed."""
    print("Checking for Visual Studio Build Tools...")

    # multiple possible locations for MSBuild
    msbuild_paths = [
        shutil.which("MSBuild"),
        r"C:\Program Files (x86)\Microsoft Visual Studio\2022\BuildTools\MSBuild\Current\Bin\MSBuild.exe",
        # r"C:\Program Files (x86)\Microsoft Visual Studio\2019\BuildTools\MSBuild\Current\Bin\MSBuild.exe",
        # r"C:\Program Files (x86)\Microsoft Visual Studio\2017\BuildTools\MSBuild\15.0\Bin\MSBuild.exe"
    ]

    # if any MSBuild installation exists
    existing_msbuild = None
    for path in msbuild_paths:
        if path and os.path.exists(path):
            existing_msbuild = path
            break

    if existing_msbuild:
        print(f"Visual Studio Build Tools already installed. Found MSBuild at: {existing_msbuild}")
        return

    # if the installer has already been run by looking for VS installation
    vs_install_paths = [
        r"C:\Program Files (x86)\Microsoft Visual Studio\2022\BuildTools",
        # r"C:\Program Files (x86)\Microsoft Visual Studio\2019\BuildTools",
        # r"C:\Program Files (x86)\Microsoft Visual Studio\2017\BuildTools"
    ]

    for vs_path in vs_install_paths:
        if os.path.exists(vs_path):
            print(f"Visual Studio Build Tools already installed at: {vs_path}")
            return

    if not os.path.exists(BUILD_TOOLS_PATH):
        raise FileNotFoundError(f"Installer not found at {BUILD_TOOLS_PATH}")

    print("Installing Visual Studio Build Tools...")
    # Need around minimum 3.5-4 gb free space to install all these(If possible keep 5 gb)
    run_command([
        BUILD_TOOLS_PATH,
         "--wait", "--nocache", "--passive", "--norestart",
        "--add", "Microsoft.VisualStudio.Workload.VCTools",
        "--add", "Microsoft.VisualStudio.Component.VC.Tools.x86.x64",
        "--add", "Microsoft.VisualStudio.Component.Windows11SDK.26100",
        "--add", "Microsoft.VisualStudio.Component.CMake",
        "--add", "Microsoft.VisualStudio.Component.TestTools.BuildTools",
        "--add", "Microsoft.VisualStudio.Component.VC.ASAN",
        "--add", "Microsoft.VisualStudio.Component.Vcpkg",
    ])

    # Verify installation actually produced MSBuild or a BuildTools folder
    verified_msbuild = None
    for path in msbuild_paths:
        if path and os.path.exists(path):
            verified_msbuild = path
            break
    if not verified_msbuild and not any(os.path.exists(p) for p in vs_install_paths):
        raise RuntimeError("Visual Studio Build Tools installation appears to have failed: MSBuild not found")

    print("Visual Studio Build Tools installed successfully.")

    # Add MSBuild path
    vs_path = r"C:\Program Files (x86)\Microsoft Visual Studio\2022\BuildTools\MSBuild\Current\Bin"
    add_to_path(vs_path)

def install_python_deps():
    """Install Python dependencies from requirements.txt."""
    if not os.path.exists(REQUIREMENTS_PATH):
        raise FileNotFoundError("requirements.txt not found; cannot install Python dependencies")
    print("Installing Python dependencies...")
    run_command([sys.executable, "-m", "pip", "install", "--upgrade", "pip"])
    run_command([sys.executable, "-m", "pip", "install", "-r", REQUIREMENTS_PATH])

def main():
    os_type = platform.system()
    print(f"Detected OS: {os_type}")

    if os_type == "Windows":
        install_build_tools_windows()
    else:
        print("Skipping Build Tools installation (non-Windows system).")

    install_python_deps()
    print("\nSetup complete!")

if __name__ == "__main__":
    main()
