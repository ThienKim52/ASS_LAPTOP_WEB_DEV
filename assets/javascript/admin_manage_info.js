// admin_manage_info.js

document.addEventListener('DOMContentLoaded', function() {
    loadAllSettings();
    setupFormHandlers();
<<<<<<< HEAD
=======
    setupImageUploadHandlers();
>>>>>>> origin/article-feature
});

// Load all settings when page loads
async function loadAllSettings() {
    try {
        const response = await fetch('ajax/settings_handler.php?action=get');
        const result = await response.json();
        
        if (result.success) {
            populateFields(result.data);
        } else {
<<<<<<< HEAD
            showToast('error', result.message || 'Lỗi khi tải dữ liệu');
        }
    } catch (error) {
        console.error('Error loading settings:', error);
        showToast('error', 'Lỗi kết nối máy chủ');
    }
}

// Populate form fields with loaded data
function populateFields(settings) {
    for (const group in settings) {
        const groupSettings = settings[group];
        if (typeof groupSettings === 'object' && groupSettings !== null) {
            for (const key in groupSettings) {
                const fieldId = group + '_' + key;
                let field = document.getElementById(fieldId);
                
                // Fallback to searching by name in that specific group's form
                if (!field) {
                    field = document.querySelector(`form[data-group="${group}"] [name="${key}"]`);
                }
                
                if (field && field.type !== 'file') {
                    field.value = groupSettings[key] || '';
                }

                const previewId = fieldId + '_preview';
                const preview = document.getElementById(previewId);
                if (preview && groupSettings[key]) {
                    preview.src = groupSettings[key];
                    preview.style.display = 'block';
                }
=======
            showToast('error', result.message || 'Error loading data');
        }
    } catch (error) {
        console.error('Error loading settings:', error);
        showToast('error', 'Server connection error');
    }
}

// Populate form fields with loaded data (JSON structure)
function populateFields(settings) {
    // Loop through each group in settings
    for (const group in settings) {
        const groupSettings = settings[group];
        
        // Loop through each setting in the group
        for (const key in groupSettings) {
            // Create field ID: group_key (e.g., "general_site_name")
            const fieldId = group + '_' + key;
            const field = document.getElementById(fieldId);
            
            if (field) {
                field.value = groupSettings[key] || '';
>>>>>>> origin/article-feature
            }
        }
    }
}

// Setup form submit handlers
function setupFormHandlers() {
<<<<<<< HEAD
    const forms = document.querySelectorAll('form[data-group]');
    forms.forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = form.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Đang lưu...';
            
            try {
                await saveSettings(form);
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        });
    });
}

// Save settings from a form
async function saveSettings(form) {
    const group = form.dataset.group;
    const settings = {};
    
    // 1. Handle Image Uploads first
    const imageFields = [
        { fileInputId: 'general_site_logo_file', settingKey: 'site_logo' },
        { fileInputId: 'home_banner_1_image_file', settingKey: 'banner_1_image' },
        { fileInputId: 'home_banner_2_image_file', settingKey: 'banner_2_image' },
        { fileInputId: 'home_banner_3_image_file', settingKey: 'banner_3_image' },
        { fileInputId: 'home_banner_4_image_file', settingKey: 'banner_4_image' },
        { fileInputId: 'home_sub_banner_1_image_file', settingKey: 'sub_banner_1_image' },
        { fileInputId: 'home_sub_banner_2_image_file', settingKey: 'sub_banner_2_image' },
        { fileInputId: 'home_sub_banner_3_image_file', settingKey: 'sub_banner_3_image' },
        { fileInputId: 'home_right_sidebar_image_file', settingKey: 'right_sidebar_image' },
        { fileInputId: 'home_promo_strip_image_file', settingKey: 'promo_strip_image' },
        { fileInputId: 'home_featured_sidebar_image_file', settingKey: 'featured_sidebar_image' }
    ];
    
    for (const field of imageFields) {
        const fileInput = document.getElementById(field.fileInputId);
        // Only process if the input exists in THIS form
        if (fileInput && form.contains(fileInput) && fileInput.files.length > 0) {
            const uploadFormData = new FormData();
            uploadFormData.append('image', fileInput.files[0]);
            uploadFormData.append('field_name', field.fileInputId.replace('_file', ''));
            
            const uploadResponse = await fetch('ajax/image_upload_handler.php', {
                method: 'POST',
                body: uploadFormData
            });
            
            const uploadResult = await uploadResponse.json();
            if (uploadResult.success) {
                settings[group + '_' + field.settingKey] = uploadResult.new_path;
                const previewId = field.fileInputId.replace('_file', '') + '_preview';
                const preview = document.getElementById(previewId);
                if (preview) {
                    preview.src = uploadResult.new_path;
                    preview.style.display = 'block';
                }
                fileInput.value = ''; // Clear for next time
            } else {
                showToast('error', 'Lỗi upload ảnh: ' + uploadResult.message);
                return;
=======
    const forms = ['form-general', 'form-header', 'form-home', 'form-contact', 'form-footer', 'form-shop'];
    
    forms.forEach(formId => {
        const form = document.getElementById(formId);
        if (form) {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                await saveSettings(form);
            });
        }
    });
}

// Setup image upload handlers (no longer needed - uploads happen on form submit)
function setupImageUploadHandlers() {
    // No automatic upload - files will be uploaded when form is submitted
}


// Save settings from a form
async function saveSettings(form) {
    const formData = new FormData(form);
    const group = form.dataset.group;
    const settings = {};
    
    // Check if there are any image files to upload first
    const imageFields = [
        { fileInputId: 'general_site_logo_file', fieldName: 'general_site_logo', settingKey: 'site_logo', group: 'general' },
        { fileInputId: 'home_banner_1_image_file', fieldName: 'home_banner_1_image', settingKey: 'banner_1_image', group: 'home' },
        { fileInputId: 'home_banner_2_image_file', fieldName: 'home_banner_2_image', settingKey: 'banner_2_image', group: 'home' }
    ];
    
    // Upload images first if any
    for (const field of imageFields) {
        if (field.group === group) {
            const fileInput = document.getElementById(field.fileInputId);
            if (fileInput && fileInput.files.length > 0) {
                showToast('info', 'Uploading image...');
                
                const uploadFormData = new FormData();
                uploadFormData.append('image', fileInput.files[0]);
                uploadFormData.append('field_name', field.fieldName);
                
                try {
                    const uploadResponse = await fetch('ajax/image_upload_handler.php', {
                        method: 'POST',
                        body: uploadFormData
                    });
                    
                    const responseText = await uploadResponse.text();
                    const uploadResult = JSON.parse(responseText);
                    
                    if (uploadResult.success) {
                        // Add uploaded image path to settings
                        settings[group + '_' + field.settingKey] = uploadResult.new_path;
                        // Clear file input
                        fileInput.value = '';
                    } else {
                        showToast('error', uploadResult.message);
                        return;
                    }
                } catch (error) {
                    showToast('error', 'Image upload failed: ' + error.message);
                    return;
                }
>>>>>>> origin/article-feature
            }
        }
    }
    
<<<<<<< HEAD
    // 2. Collect all other text/select inputs
    const formData = new FormData(form);
    for (const [key, value] of formData.entries()) {
        settings[group + '_' + key] = value;
    }
    
    // 3. Send to server
    try {
        const response = await fetch('ajax/settings_handler.php?action=save', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
=======
    // Build settings object from text inputs
    for (const [key, value] of formData.entries()) {
        // Skip file inputs
        if (!key.includes('_file')) {
            settings[group + '_' + key] = value;
        }
    }
    
    try {
        const response = await fetch('ajax/settings_handler.php?action=save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
>>>>>>> origin/article-feature
            body: JSON.stringify({ settings })
        });
        
        const result = await response.json();
<<<<<<< HEAD
        if (result.success) {
            showToast('success', 'Đã lưu thay đổi thành công!');
            loadAllSettings(); // Refresh UI
        } else {
            showToast('error', 'Lỗi: ' + result.message);
        }
    } catch (error) {
        console.error('Save error:', error);
        showToast('error', 'Lỗi kết nối khi lưu dữ liệu');
    }
}

function showToast(type, message) {
    document.querySelectorAll('.alert-toast').forEach(t => t.remove());
    const toast = document.createElement('div');
    const bgClass = type === 'success' ? 'alert-success' : type === 'info' ? 'alert-info' : 'alert-danger';
    toast.className = `alert ${bgClass} alert-dismissible alert-toast`;
    toast.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;min-width:300px;box-shadow:0 4px 12px rgba(0,0,0,0.15);';
    
    const icon = type === 'success' ? 'check-circle' : 'exclamation-circle';
    toast.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="bi bi-${icon} me-2 fs-5"></i>
            <div>${message}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(toast);
    setTimeout(() => { if (toast.parentNode) toast.remove(); }, 3000);
=======
        
        if (result.success) {
            showToast('success', result.message || 'Saved successfully!');
        } else {
            showToast('error', result.message || 'Error saving data');
        }
    } catch (error) {
        console.error('Error saving settings:', error);
        showToast('error', 'Server connection error');
    }
}

// Show toast notification
function showToast(type, message) {
    // Remove existing toasts
    document.querySelectorAll('.alert-toast').forEach(t => t.remove());
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : type === 'info' ? 'info' : 'danger'} alert-dismissible alert-toast`;
    toast.style.position = 'fixed';
    toast.style.top = '20px';
    toast.style.right = '20px';
    toast.style.zIndex = '9999';
    toast.style.minWidth = '300px';
    
    const iconMap = {
        'success': 'check',
        'error': 'alert-circle',
        'info': 'info-circle'
    };
    
    const titleMap = {
        'success': 'Success',
        'error': 'Error',
        'info': 'Info'
    };
    
    toast.innerHTML = `
        <div class="d-flex">
            <div>
                <i class="ti ti-${iconMap[type] || 'info-circle'} me-2"></i>
            </div>
            <div>
                <h4 class="alert-title">${titleMap[type] || 'Notification'}!</h4>
                <div class="text-secondary">${message}</div>
            </div>
        </div>
        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
    `;
    
    document.body.appendChild(toast);
    
    // Auto remove after 3 seconds (except for info which stays longer)
    setTimeout(() => {
        toast.remove();
    }, type === 'info' ? 5000 : 3000);
>>>>>>> origin/article-feature
}
