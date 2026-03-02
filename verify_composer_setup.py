#!/usr/bin/env python3
"""
验证 Composer 配置完整性
检查所有必要的文件是否已创建并配置正确
"""

import os
import json
import sys
from pathlib import Path

def check_file_exists(filepath, description):
    """检查文件是否存在"""
    if os.path.exists(filepath):
        print(f"✅ {description}: 存在")
        return True
    else:
        print(f"❌ {description}: 不存在")
        return False

def check_file_content(filepath, check_func, description):
    """检查文件内容"""
    if not os.path.exists(filepath):
        print(f"❌ {description}: 文件不存在")
        return False
    
    try:
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()
        
        if check_func(content):
            print(f"✅ {description}: 内容正确")
            return True
        else:
            print(f"⚠️  {description}: 内容可能有问题")
            return False
    except Exception as e:
        print(f"❌ {description}: 读取失败 - {e}")
        return False

def check_composer_json(content):
    """检查 composer.json 内容"""
    try:
        data = json.loads(content)
        
        # 检查必要字段
        required_fields = ["name", "require", "autoload"]
        for field in required_fields:
            if field not in data:
                return False
        
        # 检查 Laravel 11 依赖
        if "laravel/framework" not in data.get("require", {}):
            return False
        
        # 检查 PHP 版本要求
        if data.get("require", {}).get("php", "") != "^8.2":
            return False
        
        return True
    except:
        return False

def check_readme_has_composer_section(content):
    """检查 README 是否有 Composer 配置章节"""
    return "Composer 配置说明" in content or "composer.json" in content

def check_script_executable(filepath):
    """检查脚本是否可执行"""
    if not os.path.exists(filepath):
        return False
    
    # 检查文件权限
    import stat
    st = os.stat(filepath)
    return bool(st.st_mode & stat.S_IXUSR)

def main():
    print("🔍 验证 MMTech Laravel 项目 Composer 配置完整性")
    print("=" * 60)
    
    project_dir = Path(__file__).parent
    checks_passed = 0
    total_checks = 0
    
    # 检查 composer.json
    total_checks += 1
    if check_file_exists(project_dir / "composer.json", "composer.json 配置文件"):
        total_checks += 1
        if check_file_content(project_dir / "composer.json", check_composer_json, "composer.json 内容验证"):
            checks_passed += 2
    
    # 检查指南文档
    total_checks += 1
    if check_file_exists(project_dir / "COMPOSER_SETUP_GUIDE.md", "Composer 安装指南"):
        checks_passed += 1
    
    # 检查检查脚本
    total_checks += 1
    check_script_path = project_dir / "check_composer_setup.sh"
    if check_file_exists(check_script_path, "配置检查脚本"):
        total_checks += 1
        if check_script_executable(check_script_path):
            print("✅ 配置检查脚本: 可执行")
            checks_passed += 1
        else:
            print("⚠️  配置检查脚本: 不可执行 (需要 chmod +x)")
    
    # 检查安装脚本
    total_checks += 1
    setup_script_path = project_dir / "setup_composer.sh"
    if check_file_exists(setup_script_path, "自动安装脚本"):
        total_checks += 1
        if check_script_executable(setup_script_path):
            print("✅ 自动安装脚本: 可执行")
            checks_passed += 1
        else:
            print("⚠️  自动安装脚本: 不可执行 (需要 chmod +x)")
    
    # 检查 README 更新
    total_checks += 1
    if check_file_exists(project_dir / "README.md", "项目 README"):
        total_checks += 1
        if check_file_content(project_dir / "README.md", check_readme_has_composer_section, "README Composer 章节"):
            checks_passed += 2
    
    # 检查 Git 状态
    print("\n📊 检查 Git 状态...")
    try:
        import subprocess
        result = subprocess.run(
            ["git", "status", "--porcelain"],
            cwd=project_dir,
            capture_output=True,
            text=True
        )
        
        if result.returncode == 0:
            if result.stdout.strip():
                print("⚠️  Git 有未提交的更改:")
                print(result.stdout)
            else:
                print("✅ Git 工作区干净")
                checks_passed += 1
        else:
            print("❌ Git 命令执行失败")
        
        total_checks += 1
    except Exception as e:
        print(f"❌ 检查 Git 状态失败: {e}")
    
    # 总结
    print("\n" + "=" * 60)
    print("📋 验证结果总结")
    print(f"通过检查: {checks_passed}/{total_checks}")
    
    if checks_passed == total_checks:
        print("🎉 所有检查通过！Composer 配置完整。")
        print("\n🚀 下一步:")
        print("  1. 运行检查脚本: ./check_composer_setup.sh")
        print("  2. 根据指南安装: 参考 COMPOSER_SETUP_GUIDE.md")
        print("  3. 运行安装脚本: ./setup_composer.sh")
        return 0
    else:
        print("⚠️  部分检查未通过，需要修复。")
        return 1

if __name__ == "__main__":
    sys.exit(main())