/**
 * Quill.js Configuration for Article Content Editor
 * Rich text editor with custom toolbar and features
 */

// Quill.js configuration
const quillConfig = {
    theme: 'snow',
    placeholder: 'Write your article content here...',
    modules: {
        toolbar: {
            container: [
                // Text formatting
                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                [{ 'font': [] }],
                [{ 'size': ['small', false, 'large', 'huge'] }],
                
                // Text style
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'script': 'sub'}, { 'script': 'super' }],
                
                // Paragraph formatting
                [{ 'align': [] }],
                [{ 'indent': '-1'}, { 'indent': '+1' }],
                [{ 'direction': 'rtl' }],
                
                // Lists
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                
                // Links and media
                ['link', 'image', 'video'],
                
                // Blocks
                ['blockquote', 'code-block'],
                
                // Clear formatting
                ['clean']
            ],
            handlers: {
                // Custom image handler
                image: function() {
                    selectLocalImage();
                }
            }
        },
        // Enable syntax highlighting for code blocks
        syntax: true
    }
};

// Initialize Quill editor
function initializeQuillEditor(containerId, hiddenInputId, initialContent = '') {
    const quill = new Quill(containerId, quillConfig);
    
    // Set initial content if provided
    if (initialContent) {
        quill.root.innerHTML = initialContent;
    }
    
    // Sync Quill content with hidden input on text change
    quill.on('text-change', function() {
        const content = quill.root.innerHTML;
        document.getElementById(hiddenInputId).value = content;
    });
    
    // Handle form submission
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function() {
            // Ensure content is synced before submission
            const content = quill.root.innerHTML;
            document.getElementById(hiddenInputId).value = content;
            
            // Validate content is not empty
            const textContent = quill.getText().trim();
            if (textContent.length === 0) {
                alert('Please enter some content for the article.');
                return false;
            }
        });
    }
    
    return quill;
}

// Custom image upload handler
function selectLocalImage() {
    const input = document.createElement('input');
    input.setAttribute('type', 'file');
    input.setAttribute('accept', 'image/*');
    input.click();

    input.onchange = function() {
        const file = input.files[0];
        
        if (file) {
            // Validate file size (max 5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('Image size should be less than 5MB');
                return;
            }
            
            // Validate file type
            if (!file.type.startsWith('image/')) {
                alert('Please select a valid image file');
                return;
            }
            
            // Convert to base64 and insert
            const reader = new FileReader();
            reader.onload = function(e) {
                const range = window.quillEditor.getSelection();
                window.quillEditor.insertEmbed(range.index, 'image', e.target.result);
            };
            reader.readAsDataURL(file);
        }
    };
}

// Auto-save functionality (optional)
function enableAutoSave(quill, saveUrl, articleId = null) {
    let autoSaveTimer;
    
    quill.on('text-change', function() {
        // Clear existing timer
        if (autoSaveTimer) {
            clearTimeout(autoSaveTimer);
        }
        
        // Set new timer for auto-save (5 seconds after last change)
        autoSaveTimer = setTimeout(function() {
            const content = quill.root.innerHTML;
            const title = document.querySelector('input[name="title"]').value;
            
            if (content.trim() && title.trim()) {
                autoSaveContent(saveUrl, articleId, title, content);
            }
        }, 5000);
    });
}

// Auto-save function
function autoSaveContent(saveUrl, articleId, title, content) {
    const formData = new FormData();
    formData.append('title', title);
    formData.append('content', content);
    formData.append('auto_save', '1');
    
    if (articleId) {
        formData.append('id', articleId);
    }
    
    fetch(saveUrl, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show subtle auto-save indicator
            showAutoSaveIndicator();
        }
    })
    .catch(error => {
        console.log('Auto-save failed:', error);
    });
}

// Show auto-save indicator
function showAutoSaveIndicator() {
    let indicator = document.getElementById('auto-save-indicator');
    if (!indicator) {
        indicator = document.createElement('div');
        indicator.id = 'auto-save-indicator';
        indicator.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #28a745;
            color: white;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 12px;
            z-index: 9999;
            opacity: 0;
            transition: opacity 0.3s ease;
        `;
        indicator.textContent = 'Auto-saved';
        document.body.appendChild(indicator);
    }
    
    // Show indicator
    indicator.style.opacity = '1';
    
    // Hide after 2 seconds
    setTimeout(() => {
        indicator.style.opacity = '0';
    }, 2000);
}

// Word count functionality
function addWordCount(quill, containerId) {
    const wordCountContainer = document.createElement('div');
    wordCountContainer.id = 'word-count';
    wordCountContainer.style.cssText = `
        text-align: right;
        color: #666;
        font-size: 12px;
        margin-top: 5px;
        padding: 5px 0;
    `;
    
    const editorContainer = document.querySelector(containerId);
    editorContainer.parentNode.insertBefore(wordCountContainer, editorContainer.nextSibling);
    
    function updateWordCount() {
        const text = quill.getText();
        const words = text.trim().split(/\s+/).filter(word => word.length > 0).length;
        const chars = text.length;
        wordCountContainer.textContent = `${words} words, ${chars} characters`;
    }
    
    quill.on('text-change', updateWordCount);
    updateWordCount(); // Initial count
}