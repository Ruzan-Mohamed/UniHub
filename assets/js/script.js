

document.addEventListener('DOMContentLoaded', () => {
    // -------------------------------------------------------------
   
    // -------------------------------------------------------------
    const searchInput = document.querySelector('.search-input-group input, input[type="search"]');
    const resourceCards = document.querySelectorAll('.resource-card');

    if (searchInput && resourceCards.length > 0) {

        const cardsContainer = resourceCards[0].closest('.row');
        const noResultsMessage = document.createElement('div');
        noResultsMessage.className = 'col-12 text-center py-5 no-results-msg d-none';
        noResultsMessage.innerHTML = `
            <div class="text-muted">
                <i class="bi bi-file-earmark-x fs-1 mb-3 d-block text-primary"></i>
                <h5 class="fw-bold text-dark">No matching resources found</h5>
                <p class="small">Try adjusting your keywords, course codes, or tags.</p>
            </div>
        `;
        if (cardsContainer) {
            cardsContainer.appendChild(noResultsMessage);
        }

        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase().trim();
            let visibleCount = 0;

            resourceCards.forEach(card => {
                const title = card.querySelector('h6')?.textContent.toLowerCase() || '';
                const code = card.querySelector('.text-muted')?.textContent.toLowerCase() || '';
                const tags = Array.from(card.querySelectorAll('.badge')).map(b => b.textContent.toLowerCase()).join(' ');

                if (title.includes(query) || code.includes(query) || tags.includes(query)) {
                    card.closest('.col-lg-4, .col-md-6, .col-12').classList.remove('d-none');
                    visibleCount++;
                } else {
                    card.closest('.col-lg-4, .col-md-6, .col-12').classList.add('d-none');
                }
            });

            if (visibleCount === 0) {
                noResultsMessage.classList.remove('d-none');
            } else {
                noResultsMessage.classList.add('d-none');
            }
        });
    }


    const accountCards = document.querySelectorAll('.account-type-card');

    if (accountCards.length > 0) {
        accountCards.forEach(card => {
            card.addEventListener('click', () => {
                accountCards.forEach(c => {
                    c.classList.remove('active');
                    // Reset icon color
                    const icon = c.querySelector('i');
                    if (icon) {
                        icon.classList.add('text-muted');
                    }
                });

                card.classList.add('active');
                const icon = card.querySelector('i');
                if (icon) {
                    icon.classList.remove('text-muted');
                }
            });
        });
    }


    const nextStepBtn = document.getElementById('btnNextStep');
    const prevStepBtn = document.getElementById('btnPrevStep');
    const step1Container = document.getElementById('registerStep1');
    const step2Container = document.getElementById('registerStep2');
    const stepCircles = document.querySelectorAll('.stepper-container .step-circle');
    const stepLabels = document.querySelectorAll('.stepper-container span');

    if (nextStepBtn && step1Container && step2Container) {
        nextStepBtn.addEventListener('click', (e) => {
            const requiredInputs = step1Container.querySelectorAll('input[required], select[required]');
            let allValid = true;

            requiredInputs.forEach(input => {
                if (!input.checkValidity()) {
                    input.reportValidity();
                    allValid = false;
                }
            });

            if (allValid) {
                step1Container.classList.add('d-none');
                step2Container.classList.remove('d-none');
                
               
                if (stepCircles.length >= 2) {
                    stepCircles[0].classList.remove('active');
                    stepCircles[1].classList.add('active');
                }
                if (stepLabels.length >= 2) {
                    stepLabels[0].classList.remove('text-dark', 'fw-bold');
                    stepLabels[0].classList.add('text-muted', 'fw-semibold');
                    stepLabels[1].classList.remove('text-muted', 'fw-semibold');
                    stepLabels[1].classList.add('text-dark', 'fw-bold');
                }
            }
        });

        if (prevStepBtn) {
            prevStepBtn.addEventListener('click', () => {
                step2Container.classList.add('d-none');
                step1Container.classList.remove('d-none');

                if (stepCircles.length >= 2) {
                    stepCircles[1].classList.remove('active');
                    stepCircles[0].classList.add('active');
                }
                if (stepLabels.length >= 2) {
                    stepLabels[1].classList.remove('text-dark', 'fw-bold');
                    stepLabels[1].classList.add('text-muted', 'fw-semibold');
                    stepLabels[0].classList.remove('text-muted', 'fw-semibold');
                    stepLabels[0].classList.add('text-dark', 'fw-bold');
                }
            });
        }
    }

    const dropZone = document.querySelector('.drag-drop-zone');
    const fileInput = document.getElementById('upFileSelector'); 
    const previewBox = document.querySelector('.thumbnail-preview-box');

    if (dropZone) {
        
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
            document.body.addEventListener(eventName, preventDefaults, false);
        });


        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.add('drag-active');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.remove('drag-active');
            }, false);
        });

        dropZone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files.length > 0) {
                handleUploadedFile(files[0]);
            }
        });

        dropZone.addEventListener('click', () => {
            if (fileInput) {
                fileInput.click();
            } else {
                const tempInput = document.createElement('input');
                tempInput.type = 'file';
                tempInput.accept = '.pdf,.docx,.doc,.zip,.pptx,.ppt';
                tempInput.addEventListener('change', (e) => {
                    if (e.target.files.length > 0) {
                        handleUploadedFile(e.target.files[0]);
                    }
                });
                tempInput.click();
            }
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        function handleUploadedFile(file) {
            if (!previewBox) return;

            const name = file.name;
            const sizeKB = (file.size / 1024).toFixed(1);
            let fileIcon = 'bi-file-earmark';

            if (name.endsWith('.pdf')) fileIcon = 'bi-file-earmark-pdf-fill text-danger';
            else if (name.endsWith('.zip')) fileIcon = 'bi-file-earmark-zip-fill text-warning';
            else if (name.match(/\.(docx|doc)$/)) fileIcon = 'bi-file-earmark-word-fill text-primary';
            else if (name.match(/\.(pptx|ppt)$/)) fileIcon = 'bi-file-earmark-ppt-fill text-danger';

            previewBox.innerHTML = `
                <div class="text-center p-3">
                    <i class="bi ${fileIcon} fs-1 mb-2 d-block"></i>
                    <span class="d-block fw-bold text-dark text-truncate small px-2" style="max-width: 100%;">${name}</span>
                    <span class="text-muted small">${sizeKB} KB</span>
                    <button type="button" class="btn btn-sm btn-outline-danger mt-2 py-1 px-3 remove-preview-btn">Clear File</button>
                </div>
            `;

            previewBox.querySelector('.remove-preview-btn').addEventListener('click', (e) => {
                e.stopPropagation();
                resetUploadPreview();
            });
        }

        function resetUploadPreview() {
            if (previewBox) {
                previewBox.innerHTML = '<span class="small fw-semibold">No Preview Available</span>';
            }
            if (fileInput) {
                fileInput.value = '';
            }
        }
    }

    const bookmarkButtons = document.querySelectorAll('.resource-card button.btn-link, .resource-card .bi-bookmark, .resource-card .bi-bookmark-fill');

    bookmarkButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const icon = btn.querySelector('i') || btn;
            if (icon.classList.contains('bi-bookmark')) {
                icon.classList.remove('bi-bookmark');
                icon.classList.add('bi-bookmark-fill');
                showNotification('Item added to bookmarks!', 'success');
            } else if (icon.classList.contains('bi-bookmark-fill')) {
                icon.classList.remove('bi-bookmark-fill');
                icon.classList.add('bi-bookmark');
                showNotification('Item removed from bookmarks', 'info');

                const pageCard = btn.closest('.col-md-6, .col-lg-4');
                if (pageCard && window.location.pathname.includes('bookmarks.html')) {
                    pageCard.style.transition = 'opacity 0.3s ease';
                    pageCard.style.opacity = '0';
                    setTimeout(() => {
                        pageCard.remove();
                        const remaining = document.querySelectorAll('.resource-card');
                        if (remaining.length === 0) {
                            const cardsContainer = document.querySelector('.row');
                            if (cardsContainer) {
                                const noResultsMessage = document.createElement('div');
                                noResultsMessage.className = 'col-12 text-center py-5 no-results-msg';
                                noResultsMessage.innerHTML = `
                                    <div class="text-muted">
                                        <i class="bi bi-bookmark-x fs-1 mb-3 d-block text-primary"></i>
                                        <h5 class="fw-bold text-dark">No bookmarks found</h5>
                                        <p class="small">Bookmark resources to find them here later.</p>
                                    </div>
                                `;
                                cardsContainer.appendChild(noResultsMessage);
                            }
                        }
                    }, 300);
                }
            }
        });
    });

    const contactForm = document.querySelector('form');
    if (contactForm && window.location.pathname.includes('contact.html')) {
        contactForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const subject = document.getElementById('conSub')?.value || 'Support Ticket';
            
            showNotification(`Ticket submitted successfully! Topic: "${subject}". ID: #${Math.floor(100000 + Math.random() * 900000)}`, 'success');
            setTimeout(() => {
                window.location.href = 'dashboard.html';
            }, 2500);
        });
    }

    const caTableBody = document.getElementById('caCalculatorBody');
    const gpaTableBody = document.getElementById('gpaCalculatorBody');
    const caTotalDisplay = document.getElementById('caTotalDisplay');
    const gpaTotalDisplay = document.getElementById('gpaTotalDisplay');
    const gpaStatusBadge = document.getElementById('gpaStatusBadge');

    const GRADE_POINTS = {
        'A+': 4.00,
        'A': 4.00,
        'A-': 3.70,
        'B+': 3.30,
        'B': 3.00,
        'C+': 2.70,
        'C': 2.30,
        'F': 0.00
    };

    function getLetterGrade(score) {
        if (score >= 90) return 'A+';
        if (score >= 85) return 'A';
        if (score >= 80) return 'A-';
        if (score >= 75) return 'B+';
        if (score >= 70) return 'B';
        if (score >= 65) return 'C+';
        if (score >= 60) return 'C';
        return 'F';
    }

    function getGpaStanding(gpa) {
        if (gpa >= 3.70) return 'First Class Honours Standing';
        if (gpa >= 3.30) return 'Upper Second Class Standing';
        if (gpa >= 3.00) return 'Lower Second Class Standing';
        if (gpa >= 2.00) return 'Pass Standing';
        return 'Academic Probation Risk';
    }

    function updateCaRow(row) {
        const inputs = row.querySelectorAll('input');
        const weight = parseFloat(inputs[1]?.value) || 0;
        const marks = parseFloat(inputs[2]?.value) || 0;
        const weighted = (marks / 100) * weight;
        const scoreCell = row.querySelector('td:nth-child(4) span');
        if (scoreCell) {
            scoreCell.textContent = `${weighted.toFixed(1)} / ${weight.toFixed(1)}`;
        }
        return weighted;
    }

    function calculateCaTotal() {
        if (!caTableBody || !caTotalDisplay) return;

        const rows = caTableBody.querySelectorAll('.calculator-row');
        let totalWeighted = 0;
        let totalWeight = 0;

        rows.forEach(row => {
            const inputs = row.querySelectorAll('input');
            const weight = parseFloat(inputs[1]?.value) || 0;
            totalWeighted += updateCaRow(row);
            totalWeight += weight;
        });

        if (totalWeight === 0) {
            caTotalDisplay.textContent = '0.0 / 100 (Grade: F)';
            return;
        }

        const normalizedScore = (totalWeighted / totalWeight) * 100;
        const grade = getLetterGrade(normalizedScore);
        caTotalDisplay.textContent = `${normalizedScore.toFixed(1)} / 100 (Grade: ${grade})`;
    }

    function calculateGpaTotal() {
        if (!gpaTableBody || !gpaTotalDisplay) return;

        const rows = gpaTableBody.querySelectorAll('.calculator-row');
        let totalPoints = 0;
        let totalCredits = 0;

        rows.forEach(row => {
            const creditSelect = row.querySelector('td:nth-child(2) select');
            const gradeSelect = row.querySelector('td:nth-child(3) select');
            const credits = parseFloat(creditSelect?.value) || 0;
            const grade = gradeSelect?.value || 'F';
            const points = GRADE_POINTS[grade] ?? 0;

            totalPoints += credits * points;
            totalCredits += credits;
        });

        const gpa = totalCredits > 0 ? totalPoints / totalCredits : 0;
        gpaTotalDisplay.textContent = gpa.toFixed(2);

        if (gpaStatusBadge) {
            gpaStatusBadge.textContent = `Status: ${getGpaStanding(gpa)}`;
        }
    }

    function createCaRow() {
        const row = document.createElement('tr');
        row.className = 'calculator-row';
        row.innerHTML = `
            <td><input type="text" class="form-control bg-light" placeholder="Component name" required></td>
            <td><input type="number" class="form-control bg-light" value="0" min="0" max="100" required></td>
            <td><input type="number" class="form-control bg-light" value="0" min="0" max="100" required></td>
            <td><span class="fw-semibold">0.0 / 0.0</span></td>
            <td><button type="button" class="btn btn-link text-danger p-0" aria-label="Delete component"><i class="bi bi-trash"></i></button></td>
        `;
        return row;
    }

    function createGpaRow() {
        const row = document.createElement('tr');
        row.className = 'calculator-row';
        row.innerHTML = `
            <td><input type="text" class="form-control bg-light" placeholder="Course code & name" required></td>
            <td>
                <select class="form-select bg-light" aria-label="Select Credits">
                    <option value="4">4 Credits</option>
                    <option value="3" selected>3 Credits</option>
                    <option value="2">2 Credits</option>
                    <option value="1">1 Credit</option>
                </select>
            </td>
            <td>
                <select class="form-select bg-light" aria-label="Select Expected Grade">
                    <option value="A+">A+ (4.00)</option>
                    <option value="A" selected>A (4.00)</option>
                    <option value="A-">A- (3.70)</option>
                    <option value="B+">B+ (3.30)</option>
                    <option value="B">B (3.00)</option>
                </select>
            </td>
            <td><button type="button" class="btn btn-link text-danger p-0" aria-label="Delete course"><i class="bi bi-trash"></i></button></td>
        `;
        return row;
    }

    if (caTableBody) {
        caTableBody.addEventListener('input', (e) => {
            if (e.target.matches('input')) {
                calculateCaTotal();
            }
        });

        caTableBody.addEventListener('click', (e) => {
            const deleteBtn = e.target.closest('button');
            if (!deleteBtn || !deleteBtn.querySelector('.bi-trash')) return;

            const rows = caTableBody.querySelectorAll('.calculator-row');
            if (rows.length <= 1) {
                showNotification('At least one CA component is required.', 'info');
                return;
            }

            deleteBtn.closest('.calculator-row')?.remove();
            calculateCaTotal();
        });

        const caForm = caTableBody.closest('form');
        if (caForm) {
            caForm.addEventListener('submit', (e) => {
                e.preventDefault();
                calculateCaTotal();
                showNotification('CA total updated successfully!', 'success');
            });
        }

        document.getElementById('btnAddCaRow')?.addEventListener('click', () => {
            caTableBody.appendChild(createCaRow());
            calculateCaTotal();
        });

        calculateCaTotal();
    }

    if (gpaTableBody) {
        gpaTableBody.addEventListener('change', () => {
            calculateGpaTotal();
        });

        gpaTableBody.addEventListener('click', (e) => {
            const deleteBtn = e.target.closest('button');
            if (!deleteBtn || !deleteBtn.querySelector('.bi-trash')) return;

            const rows = gpaTableBody.querySelectorAll('.calculator-row');
            if (rows.length <= 1) {
                showNotification('At least one course row is required.', 'info');
                return;
            }

            deleteBtn.closest('.calculator-row')?.remove();
            calculateGpaTotal();
        });

        const gpaForm = gpaTableBody.closest('form');
        if (gpaForm) {
            gpaForm.addEventListener('submit', (e) => {
                e.preventDefault();
                calculateGpaTotal();
                showNotification('Semester GPA updated successfully!', 'success');
            });
        }

        document.getElementById('btnAddGpaRow')?.addEventListener('click', () => {
            gpaTableBody.appendChild(createGpaRow());
            calculateGpaTotal();
        });

        calculateGpaTotal();
    }

    document.querySelectorAll('a[aria-label="Download resource"]').forEach(link => {
        link.addEventListener('click', (event) => {
            event.preventDefault();
            const title = link.closest('.resource-card, tr, .card')?.querySelector('h5, h6, td')?.textContent?.trim() || 'Resource';
            link.classList.add('disabled');
            link.innerHTML = '<span class="spinner-border spinner-border-sm" aria-hidden="true"></span>';
            setTimeout(() => {
                link.classList.remove('disabled');
                link.innerHTML = '<i class="bi bi-download"></i>';
                showNotification(`${title} is ready to download.`, 'success');
            }, 450);
        });
    });

    const isAdminPage = window.location.pathname.includes('/admin/');
    const deleteButtons = isAdminPage
        ? document.querySelectorAll('button[aria-label="Delete student"], button[aria-label="Delete course"], button[aria-label="Delete announcement"]')
        : [];

    deleteButtons.forEach(button => {
        button.addEventListener('click', () => {
            const item = button.closest('tr, .announcement-item, .card');
            if (!item) return;
            item.style.transition = 'opacity 0.2s ease';
            item.style.opacity = '0';
            setTimeout(() => item.remove(), 200);
            showNotification('Item removed successfully.', 'info');
        });
    });

    document.querySelectorAll('.modal form, .tab-pane form').forEach(form => {
        form.addEventListener('submit', (event) => {
            if (!form.checkValidity()) return;
            event.preventDefault();
            const modalElement = form.closest('.modal');
            if (modalElement && window.bootstrap) {
                bootstrap.Modal.getOrCreateInstance(modalElement).hide();
            }
            showNotification('Your changes have been saved.', 'success');
        });
    });

    document.querySelectorAll('button[aria-label="Upload profile image"]').forEach(button => {
        button.addEventListener('click', () => {
            const picker = document.createElement('input');
            picker.type = 'file';
            picker.accept = 'image/*';
            picker.addEventListener('change', () => {
                if (picker.files?.length) showNotification('Profile photo selected. Save your profile to apply it.', 'info');
            });
            picker.click();
        });
    });

    if (window.location.pathname.includes('/student/resources.html')) {
        const resourceGrid = document.querySelector('.resource-card')?.closest('.row');

        document.querySelectorAll('.resource-card').forEach(card => {
            const actions = card.querySelector('a[aria-label="Download resource"]')?.parentElement;
            if (!actions || actions.querySelector('[aria-label="Delete resource"]')) return;

            const deleteButton = document.createElement('button');
            deleteButton.type = 'button';
            deleteButton.className = 'btn btn-outline-danger btn-sm';
            deleteButton.setAttribute('aria-label', 'Delete resource');
            deleteButton.innerHTML = '<i class="bi bi-trash"></i>';
            deleteButton.addEventListener('click', () => {
                const title = card.querySelector('h6')?.textContent?.trim() || 'this resource';
                if (!window.confirm(`Delete ${title}? This action cannot be undone.`)) return;

                const resourceColumn = card.closest('.col-lg-4, .col-md-6, .col-12');
                resourceColumn?.remove();
                showNotification('Resource deleted successfully.', 'info');

                if (resourceGrid && !resourceGrid.querySelector('.resource-card')) {
                    const emptyState = document.createElement('div');
                    emptyState.className = 'col-12 text-center py-5';
                    emptyState.innerHTML = '<i class="bi bi-folder-x fs-1 text-muted d-block mb-3"></i><h5 class="fw-bold">No resources available</h5><p class="text-muted small mb-3">Upload a resource to start building your library.</p><a href="upload.html" class="btn btn-primary btn-sm">Upload resource</a>';
                    resourceGrid.appendChild(emptyState);
                }
            });
            actions.appendChild(deleteButton);
        });
    }

    function showNotification(message, type = 'success') {
        let toastContainer = document.querySelector('.unihub-toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'unihub-toast-container position-fixed bottom-0 end-0 p-3';
            toastContainer.style.zIndex = '9999';
            document.body.appendChild(toastContainer);
        }

        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'primary' : type === 'info' ? 'info' : 'danger'} border-0 show mb-2`;
        toast.role = 'alert';
        toast.ariaLive = 'assertive';
        toast.ariaAtomic = 'true';
        toast.style.borderRadius = '8px';
        toast.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.15)';

        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2 py-3 px-4">
                    <i class="bi ${type === 'success' ? 'bi-check-circle-fill' : type === 'info' ? 'bi-info-circle-fill' : 'bi-exclamation-triangle-fill'} fs-5"></i>
                    <span class="small fw-semibold">${message}</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;

        toastContainer.appendChild(toast);

        setTimeout(() => {
            toast.style.transition = 'opacity 0.5s ease';
            toast.style.opacity = '0';
            setTimeout(() => {
                toast.remove();
                if (toastContainer.children.length === 0) {
                    toastContainer.remove();
                }
            }, 500);
        }, 3500);

        toast.querySelector('.btn-close').addEventListener('click', () => {
            toast.remove();
        });
    }
});


