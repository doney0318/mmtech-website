// 步骤管理
function showStep(step) {
    // 更新步骤状态
    document.querySelectorAll('.step').forEach((el, index) => {
        el.classList.remove('active', 'completed');
        if (index + 1 < step) {
            el.classList.add('completed');
        } else if (index + 1 === step) {
            el.classList.add('active');
        }
    });
    
    // 显示对应内容
    document.querySelectorAll('.step-content').forEach((el, index) => {
        el.classList.remove('active');
        if (index + 1 === step) {
            el.classList.add('active');
        }
    });
}

// 环境检测
function checkEnvironment() {
    const btn = document.getElementById('env-check-btn');
    const alert = document.getElementById('env-alert');
    const envCheck = document.getElementById('env-check');
    
    btn.disabled = true;
    btn.textContent = '检测中...';
    alert.style.display = 'none';
    
    fetch('./install_check.php')
        .then(res => {
            if (!res.ok) {
                throw new Error(`HTTP ${res.status}: ${res.statusText}`);
            }
            return res.json();
        })
        .then(data => {
            envCheck.innerHTML = '';
            
            if (data.passed) {
                // 显示成功结果
                data.requirements.forEach(req => {
                    const item = document.createElement('div');
                    item.className = `requirement-item ${req.passed ? 'passed' : 'failed'}`;
                    item.innerHTML = `
                        <span class="requirement-icon">${req.passed ? '✅' : '❌'}</span>
                        <span>${req.name}: ${req.value} ${req.required ? `(要求: ${req.required})` : ''}</span>
                    `;
                    envCheck.appendChild(item);
                });
                
                alert.className = 'alert alert-success';
                alert.textContent = '✅ 环境检测通过，可以继续安装';
                alert.style.display = 'block';
                
                setTimeout(() => {
                    showStep(2);
                }, 1000);
            } else {
                // 显示失败结果
                data.requirements.forEach(req => {
                    const item = document.createElement('div');
                    item.className = `requirement-item ${req.passed ? 'passed' : 'failed'}`;
                    item.innerHTML = `
                        <span class="requirement-icon">${req.passed ? '✅' : '❌'}</span>
                        <span>${req.name}: ${req.value} ${req.required ? `(要求: ${req.required})` : ''}</span>
                    `;
                    envCheck.appendChild(item);
                });
                
                alert.className = 'alert alert-error';
                alert.textContent = '❌ 环境检测未通过，请解决以上问题';
                alert.style.display = 'block';
            }
        })
        .catch(err => {
            console.error('环境检测错误:', err);
            alert.className = 'alert alert-error';
            alert.textContent = '环境检测失败：' + err.message + '。请检查 PHP 是否正常工作。';
            alert.style.display = 'block';
        })
        .finally(() => {
            btn.disabled = false;
            btn.textContent = '重新检测';
        });
}

// 数据库测试
function testDatabase() {
    const btn = document.getElementById('db-test-btn');
    const alert = document.getElementById('db-alert');
    const form = document.getElementById('db-form');
    
    btn.disabled = true;
    btn.textContent = '测试中...';
    alert.style.display = 'none';
    
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    
    fetch('./install_test_db.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(data)
    })
    .then(res => {
        if (!res.ok) {
            throw new Error(`HTTP ${res.status}: ${res.statusText}`);
        }
        return res.json();
    })
    .then(data => {
        if (data.success) {
            alert.className = 'alert alert-success';
            alert.textContent = '✅ 数据库连接成功';
            alert.style.display = 'block';
            
            setTimeout(() => {
                showStep(3);
            }, 1000);
        } else {
            alert.className = 'alert alert-error';
            alert.textContent = '❌ ' + data.message;
            alert.style.display = 'block';
        }
    })
    .catch(err => {
        console.error('数据库测试错误:', err);
        alert.className = 'alert alert-error';
        alert.textContent = '数据库测试失败：' + err.message;
        alert.style.display = 'block';
    })
    .finally(() => {
        btn.disabled = false;
        btn.textContent = '测试连接';
    });
}

// 安装系统
function installSystem() {
    const btn = document.getElementById('install-btn');
    const alert = document.getElementById('admin-alert');
    const adminForm = document.getElementById('admin-form');
    const dbForm = document.getElementById('db-form');
    
    // 验证密码
    const password = adminForm.admin_password.value;
    const confirmPassword = adminForm.admin_password_confirm.value;
    
    if (password !== confirmPassword) {
        alert.className = 'alert alert-error';
        alert.textContent = '两次输入的密码不一致';
        alert.style.display = 'block';
        return;
    }
    
    btn.disabled = true;
    btn.textContent = '安装中...';
    alert.style.display = 'none';
    
    // 合并表单数据
    const adminData = Object.fromEntries(new FormData(adminForm));
    const dbData = Object.fromEntries(new FormData(dbForm));
    // 同时发送扁平和嵌套结构，兼容新旧安装后端
    const installData = {
        ...dbData,
        ...adminData,
        db: dbData,
        admin: adminData,
    };
    
    // 显示安装进度
    const installResult = document.getElementById('install-result');
    installResult.innerHTML = `
        <div class="alert alert-info">
            <p>正在安装系统，请稍候...</p>
            <div style="margin-top: 10px;">
                <div style="background: #e5e7eb; height: 4px; border-radius: 2px; overflow: hidden;">
                    <div id="install-progress" style="background: #2563EB; height: 100%; width: 0%; transition: width 0.3s;"></div>
                </div>
            </div>
        </div>
    `;
    
    const progressBar = document.getElementById('install-progress');
    
    fetch('./install_execute.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(installData)
    })
    .then(res => {
        if (!res.ok) {
            throw new Error(`HTTP ${res.status}: ${res.statusText}`);
        }
        return res.json();
    })
    .then(data => {
        progressBar.style.width = '100%';
        
        if (data.success) {
            // 显示成功信息
            document.getElementById('success-info').style.display = 'block';
            document.getElementById('install-result').style.display = 'none';
            document.getElementById('admin-username').textContent = adminData.admin_username;
            
            // 更新步骤状态
            document.querySelectorAll('.step').forEach(el => {
                el.classList.add('completed');
            });
            
            // 显示步骤4
            showStep(4);
        } else {
            alert.className = 'alert alert-error';
            alert.textContent = '❌ 安装失败：' + data.message;
            alert.style.display = 'block';
            btn.disabled = false;
            btn.textContent = '重新安装';
        }
    })
    .catch(err => {
        console.error('安装错误:', err);
        alert.className = 'alert alert-error';
        alert.textContent = '安装失败：' + err.message;
        alert.style.display = 'block';
        btn.disabled = false;
        btn.textContent = '重新安装';
    });
}