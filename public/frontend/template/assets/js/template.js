// ========== CATEGORY DROPDOWN FUNCTIONALITY ==========
(function () {
    'use strict';

    // State
    let hoverTimer = null;
    const isMobile = () => window.innerWidth <= 992;
    const dropdown = document.getElementById('categoryDropdown');
    const categoriesList = document.getElementById('mainCategoriesList');
    const contents = document.getElementById('categoriesContent');

    // Core Functions
    function showCategory (categoryId) {
        // Hide all content
        document.querySelectorAll('.category-content').forEach(el => el.classList.add('d-none'));

        // Show selected
        const content = document.getElementById('content-' + categoryId);
        if (content) content.classList.remove('d-none');

        // Update active state - ONLY ON DESKTOP
        if (!isMobile()) {
            document.querySelectorAll('.main-category-item').forEach(el => el.classList.remove('active'));
            const activeItem = document.querySelector(`.main-category-item[data-id="${categoryId}"]`);
            if (activeItem) activeItem.classList.add('active');
        }
    }

    function hideAll () {
        if (isMobile()) return;
        document.querySelectorAll('.category-content').forEach(el => el.classList.add('d-none'));
        document.querySelectorAll('.main-category-item').forEach(el => el.classList.remove('active'));
    }

    // Mobile: Move subcategories inside main category
    function initMobileSubcategories() {
        if (!isMobile()) return;
        
        document.querySelectorAll('.main-category-item').forEach(item => {
            const categoryId = item.dataset.id;
            const hasChildren = item.dataset.hasChildren === 'true';
            
            // Remove existing mobile subcategory wrapper if any
            const existingWrapper = item.querySelector('.mobile-subcategories-wrapper');
            if (existingWrapper) existingWrapper.remove();
            
            if (hasChildren) {
                const contentDiv = document.getElementById(`content-${categoryId}`);
                if (contentDiv) {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'mobile-subcategories-wrapper';
                    wrapper.style.display = 'none';
                    wrapper.style.background = '#f8f9fa';
                    wrapper.style.padding = '12px 16px 12px 32px';
                    wrapper.style.borderTop = '1px solid #e9ecef';
                    wrapper.style.maxHeight = 'none';
                    wrapper.style.overflow = 'visible';
                    wrapper.innerHTML = contentDiv.innerHTML;
                    item.appendChild(wrapper);
                }
            }
        });
    }

    // Mobile: Toggle subcategories
    function toggleMobileSubcategory(item) {
        const wrapper = item.querySelector('.mobile-subcategories-wrapper');
        if (!wrapper) return;
        
        if (wrapper.style.display === 'none') {
            // Close all others first
            document.querySelectorAll('.main-category-item .mobile-subcategories-wrapper').forEach(w => {
                w.style.display = 'none';
            });
            
            // Open current
            wrapper.style.display = 'block';
        } else {
            wrapper.style.display = 'none';
        }
    }

    // Event Handlers
    if (categoriesList) {
        categoriesList.addEventListener('mouseover', (e) => {
            const item = e.target.closest('.main-category-item');
            if (!item || isMobile()) return;

            const categoryId = item.dataset.id;
            const hasChildren = item.dataset.hasChildren === 'true';

            if (hoverTimer) clearTimeout(hoverTimer);

            if (hasChildren) {
                hoverTimer = setTimeout(() => showCategory(categoryId), 50);
            } else {
                hoverTimer = setTimeout(hideAll, 50);
            }
        });
    }

    if (dropdown) {
        dropdown.addEventListener('mouseenter', () => {
            if (hoverTimer) clearTimeout(hoverTimer);
        });

        dropdown.addEventListener('mouseleave', () => {
            const activeItem = document.querySelector('.main-category-item.active');
            if (activeItem?.dataset.hasChildren === 'true') return;
            hoverTimer = setTimeout(hideAll, 100);
        });
    }

    // Mobile
    if (window.innerWidth <= 992) {
        // Remove active class from all categories on mobile
        document.querySelectorAll('.main-category-item').forEach(el => el.classList.remove('active'));
        
        // Initialize mobile structure
        initMobileSubcategories();
        
        // Category button toggle
        document.querySelector('.category__button')?.addEventListener('click', (e) => {
            e.preventDefault();
            const dd = document.querySelector('.responsive-dropdown');
            if (dd) {
                if (dd.style.display === 'block') {
                    dd.style.display = 'none';
                } else {
                    dd.style.display = 'block';
                    initMobileSubcategories();
                }
            }
        });

        // Category items click - modified for mobile
        document.querySelectorAll('.main-category-item').forEach(item => {
            // Remove any existing click listeners
            item.removeEventListener('click', item.clickHandler);
            
            // Add new click handler
            const clickHandler = (e) => {
                const hasChildren = item.dataset.hasChildren === 'true';
                const link = item.querySelector('a');
                
                if (hasChildren) {
                    e.preventDefault();
                    toggleMobileSubcategory(item);
                }
                // If no children, let the link work normally
            };
            
            item.addEventListener('click', clickHandler);
            item.clickHandler = clickHandler;
        });

        // Close button
        document.querySelector('.close-responsive-dropdown')?.addEventListener('click', () => {
            document.querySelector('.responsive-dropdown').style.display = 'none';
            // Reset mobile subcategories
            document.querySelectorAll('.mobile-subcategories-wrapper').forEach(w => {
                w.style.display = 'none';
            });
        });

        // Close on outside click
        document.addEventListener('click', (e) => {
            const dd = document.querySelector('.responsive-dropdown');
            const btn = document.querySelector('.category__button');
            if (dd?.style.display === 'block' && btn && !btn.contains(e.target) && !dd.contains(e.target)) {
                dd.style.display = 'none';
                // Reset mobile subcategories
                document.querySelectorAll('.mobile-subcategories-wrapper').forEach(w => {
                    w.style.display = 'none';
                });
            }
        });
    }

    // Initialize first category - ONLY ON DESKTOP
    if (!isMobile()) {
        const firstCategory = document.querySelector('.main-category-item[data-has-children="true"]');
        if (firstCategory) showCategory(firstCategory.dataset.id);
    }

    // Update mobile state on resize
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            if (window.innerWidth > 992) {
                // Switch to desktop mode
                document.querySelector('.responsive-dropdown').style.display = '';
                // Remove mobile wrappers on desktop
                document.querySelectorAll('.mobile-subcategories-wrapper').forEach(w => w.remove());
                // Show desktop first category
                const firstCategory = document.querySelector('.main-category-item[data-has-children="true"]');
                if (firstCategory) showCategory(firstCategory.dataset.id);
            } else {
                // Switch to mobile mode
                // Remove active class
                document.querySelectorAll('.main-category-item').forEach(el => el.classList.remove('active'));
                // Re-initialize for mobile
                if (document.querySelector('.responsive-dropdown')?.style.display === 'block') {
                    initMobileSubcategories();
                }
            }
        }, 250);
    });
})();
// ========== WHATSAPP FUNCTIONS ==========
function toggleWhatsappMenu() {
    const menu = document.getElementById('whatsapp-menu');
    if (menu) {
        if (menu.classList.contains('d-none')) {
            menu.classList.remove('d-none');
        } else {
            menu.classList.add('d-none');
        }
    }
}

document.addEventListener('click', function (event) {
    const widget = document.getElementById('whatsapp-widget');
    const menu = document.getElementById('whatsapp-menu');
    if (widget && menu && !widget.contains(event.target) && !menu.classList.contains('d-none')) {
        menu.classList.add('d-none');
    }
});

// ========== COUNTDOWN FUNCTIONS ==========
document.addEventListener('DOMContentLoaded', function () {
    function updateCountdown(elementId, endTimestamp) {
        const now = new Date().getTime();
        const distance = endTimestamp * 1000 - now;
        if (distance <= 0) {
            const element = document.getElementById(elementId);
            if (element) {
                element.style.display = 'none';
            }
            return;
        }
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        const element = document.getElementById(elementId);
        if (element) {
            const daysSpan = element.querySelector('.days');
            const hoursSpan = element.querySelector('.hours');
            const minutesSpan = element.querySelector('.minutes');
            const secondsSpan = element.querySelector('.seconds');
            if (daysSpan) daysSpan.innerHTML = days;
            if (hoursSpan) hoursSpan.innerHTML = hours;
            if (minutesSpan) minutesSpan.innerHTML = minutes;
            if (secondsSpan) secondsSpan.innerHTML = seconds;
        }
    }

    function initializeAllCountdowns() {
        document.querySelectorAll('[id^="countdown-"]').forEach(countdown => {
            const id = countdown.id;
            const endDate = countdown.getAttribute('data-end-date');
            if (endDate) {
                updateCountdown(id, parseInt(endDate));
                if (!countdown.hasAttribute('data-interval-set')) {
                    setInterval(() => updateCountdown(id, parseInt(endDate)), 1000);
                    countdown.setAttribute('data-interval-set', 'true');
                }
            }
        });
    }

    initializeAllCountdowns();
    const tabButtons = document.querySelectorAll('button[data-bs-toggle="pill"]');
    tabButtons.forEach(button => {
        button.addEventListener('shown.bs.tab', function () {
            setTimeout(initializeAllCountdowns, 100);
        });
    });
});

// ========== AUTH FUNCTIONS ==========
$(document).ready(function () {
    window.authFunctions = {
        showAlert: function (message, type = 'success') {
            const alertHtml = `<div class="alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show" role="alert">
                <i class="ph ph-${type === 'success' ? 'check-circle' : 'warning-circle'} me-2"></i> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>`;
            if ($('#alertContainer').length) {
                $('#alertContainer').html(alertHtml);
                $('html, body').animate({ scrollTop: $('#alertContainer').offset().top - 100 }, 500);
            } else {
                const tempAlert = $(alertHtml);
                $('main').prepend(tempAlert);
                setTimeout(() => tempAlert.fadeOut(), 3000);
            }
        },
        togglePassword: function (button) {
            let input = $(button).prev('input');
            let icon = $(button).find('i');
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('ph-eye-slash').addClass('ph-eye');
            } else {
                input.attr('type', 'password');
                icon.removeClass('ph-eye').addClass('ph-eye-slash');
            }
        },
        clearErrors: function (form) {
            $(form).find('.invalid-feedback').text('');
            $(form).find('.form-control').removeClass('is-invalid');
        },
        showErrors: function (errors) {
            $.each(errors, function (field, messages) {
                if (field === 'image') {
                    $('#imageError').removeClass('d-none').text(messages[0]);
                } else {
                    $(`#${field}`).addClass('is-invalid');
                    $(`#${field}-error`).text(messages[0]);
                }
            });
        }
    };

    window.profileFunctions = {
        previewImage: function (input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const preview = $('#profileImagePreview');
                    if (preview.length) {
                        preview.attr('src', e.target.result);
                    } else {
                        $('#defaultProfileIcon').hide();
                        const parent = $('#defaultProfileIcon').parent();
                        const img = $('<img>', {
                            id: 'profileImagePreview',
                            class: 'rounded-circle border border-gray-200',
                            style: 'width: 150px; height: 150px; object-fit: cover;',
                            src: e.target.result
                        });
                        parent.prepend(img);
                    }
                };
                reader.readAsDataURL(input.files[0]);
            }
        },
        updateProfile: function (form) {
            const formData = new FormData(form);
            const $btn = $(form).find('button[type="submit"]');
            const $spinner = $btn.find('.spinner-border');
            const $btnText = $btn.find('.btn-text');
            window.authFunctions.clearErrors(form);
            $('#imageError').addClass('d-none').text('');
            $btn.prop('disabled', true);
            $spinner.removeClass('d-none');
            $btnText.text('Updating...');
            $.ajax({
                url: window.appConfig.routes.userProfileUpdate,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: { 'X-CSRF-TOKEN': window.appConfig.csrfToken },
                success: function (response) {
                    if (response.success) {
                        $('#sidebarProfileName').text(response.user.name);
                        $('#sidebarProfileEmail').text(response.user.email);
                        if (response.user.image) {
                            const sidebarImage = $('#sidebarProfileImage');
                            const sidebarIcon = $('#sidebarProfileIcon');
                            if (sidebarImage.length) {
                                sidebarImage.attr('src', '/storage/' + response.user.image);
                                sidebarImage.show();
                            }
                            if (sidebarIcon.length) sidebarIcon.hide();
                            const mainPreview = $('#profileImagePreview');
                            if (mainPreview.length) mainPreview.attr('src', '/storage/' + response.user.image);
                        }
                        $('#name').val(response.user.name);
                        $('#email').val(response.user.email);
                        $('#dob').val(response.user.dob ? response.user.dob.split('T')[0] : '');
                        $('#address').val(response.user.address || '');
                        window.authFunctions.showAlert('Profile updated successfully!', 'success');
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        window.authFunctions.showErrors(xhr.responseJSON.errors);
                    } else {
                        window.authFunctions.showAlert('Something went wrong!', 'error');
                    }
                },
                complete: function () {
                    $btn.prop('disabled', false);
                    $spinner.addClass('d-none');
                    $btnText.text('Update Profile');
                }
            });
        },
        changePassword: function (form) {
            const $btn = $(form).find('button[type="submit"]');
            const $spinner = $btn.find('.spinner-border');
            const $btnText = $btn.find('.btn-text');
            window.authFunctions.clearErrors(form);
            $btn.prop('disabled', true);
            $spinner.removeClass('d-none');
            $btnText.text('Changing...');
            $.ajax({
                url: window.appConfig.routes.userPasswordChange,
                type: 'POST',
                data: $(form).serialize(),
                headers: { 'X-CSRF-TOKEN': window.appConfig.csrfToken },
                success: function (response) {
                    if (response.success) {
                        $('#current_password, #new_password, #new_password_confirmation').val('');
                        window.authFunctions.showAlert(response.message, 'success');
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 401) {
                        window.authFunctions.showAlert(xhr.responseJSON.message, 'error');
                    } else if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        window.authFunctions.showErrors(xhr.responseJSON.errors);
                    } else {
                        window.authFunctions.showAlert('Something went wrong!', 'error');
                    }
                },
                complete: function () {
                    $btn.prop('disabled', false);
                    $spinner.addClass('d-none');
                    $btnText.text('Change Password');
                }
            });
        }
    };

    $(document).on('click', '.toggle-password', function () {
        window.authFunctions.togglePassword(this);
    });
    $(document).on('change', '#imageUpload', function (e) {
        window.profileFunctions.previewImage(this);
    });
    $(document).on('submit', '#profileUpdateForm', function (e) {
        e.preventDefault();
        window.profileFunctions.updateProfile(this);
    });
    $(document).on('submit', '#passwordChangeForm', function (e) {
        e.preventDefault();
        window.profileFunctions.changePassword(this);
    });
    $(document).on('submit', '#loginForm', function (e) {
        e.preventDefault();
        const $btn = $(this).find('button[type="submit"]');
        const $spinner = $btn.find('.spinner-border');
        const $btnText = $btn.find('.btn-text');
        window.authFunctions.clearErrors(this);
        $btn.prop('disabled', true);
        $spinner.removeClass('d-none');
        $btnText.text('Logging in...');
        $.ajax({
            url: window.appConfig.routes.userLogin,
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function (response) {
                if (response.success) window.location.href = response.redirect;
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    window.authFunctions.showErrors(xhr.responseJSON.errors);
                } else if (xhr.status === 401 && xhr.responseJSON && xhr.responseJSON.message) {
                    window.authFunctions.showAlert(xhr.responseJSON.message, 'error');
                }
            },
            complete: function () {
                $btn.prop('disabled', false);
                $spinner.addClass('d-none');
                $btnText.text('Login');
            }
        });
    });
    $(document).on('submit', '#registerForm', function (e) {
        e.preventDefault();
        const $btn = $(this).find('button[type="submit"]');
        const $spinner = $btn.find('.spinner-border');
        const $btnText = $btn.find('.btn-text');
        window.authFunctions.clearErrors(this);
        $btn.prop('disabled', true);
        $spinner.removeClass('d-none');
        $btnText.text('Creating account...');
        $.ajax({
            url: window.appConfig.routes.userRegister,
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function (response) {
                if (response.success) window.location.href = response.redirect;
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    window.authFunctions.showErrors(xhr.responseJSON.errors);
                } else {
                    window.authFunctions.showAlert('Something went wrong!', 'error');
                }
            },
            complete: function () {
                $btn.prop('disabled', false);
                $spinner.addClass('d-none');
                $btnText.text('Create Account');
            }
        });
    });
    $(document).on('click', '.forgot-password-link', function (e) {
        e.preventDefault();
        $('#step1').show();
        $('#step2').hide();
        $('#resetEmail').val('');
        $('#resetCode').val('');
        $('#newPassword').val('');
        $('#confirmPassword').val('');
        $('#forgotPasswordModal').modal('show');
    });
    $(document).on('click', '#sendResetCodeBtn', function () {
        let email = $('#resetEmail').val().trim();
        if (!email) {
            alert('Please enter your email');
            return;
        }
        const $btn = $(this);
        $btn.prop('disabled', true).text('Sending...');
        $.ajax({
            url: window.appConfig.routes.passwordSendCode,
            type: 'POST',
            data: { email: email, _token: window.appConfig.csrfToken },
            success: function (response) {
                if (response.success) {
                    $('#step1').hide();
                    $('#displayEmail').text(email);
                    $('#step2').show();
                    $('#resetEmail').val(email);
                } else {
                    alert(response.message || 'Error sending code');
                }
            },
            error: function (xhr) {
                alert(xhr.responseJSON?.message || 'Error sending code');
            },
            complete: function () {
                $btn.prop('disabled', false).text('Send Reset Code');
            }
        });
    });
    $(document).on('click', '#resetPasswordBtn', function () {
        let data = {
            email: $('#resetEmail').val(),
            token: $('#resetCode').val(),
            password: $('#newPassword').val(),
            password_confirmation: $('#confirmPassword').val(),
            _token: window.appConfig.csrfToken
        };
        if (!data.token || !data.password || !data.password_confirmation) {
            alert('Please fill all fields');
            return;
        }
        if (data.password !== data.password_confirmation) {
            alert('Passwords do not match');
            return;
        }
        const $btn = $(this);
        $btn.prop('disabled', true).text('Resetting...');
        $.ajax({
            url: window.appConfig.routes.passwordReset,
            type: 'POST',
            data: data,
            success: function (response) {
                if (response.success) {
                    $('#forgotPasswordModal').modal('hide');
                    window.authFunctions.showAlert('Password reset successful!', 'success');
                    setTimeout(function () {
                        window.location.href = response.redirect || window.appConfig.siteUrl;
                    }, 1500);
                } else {
                    alert(response.message || 'Error resetting password');
                }
            },
            error: function (xhr) {
                alert(xhr.responseJSON?.message || 'Error resetting password');
            },
            complete: function () {
                $btn.prop('disabled', false).text('Reset Password');
            }
        });
    });
});

// ========== CART SYSTEM ==========
window.cartSystem = {
    loadCartCount: function () {
        $.ajax({
            url: window.appConfig.routes.cartCount,
            method: 'GET',
            success: function (response) {
                if (response.success) window.cartSystem.updateNavbarCartCount(response.count);
            }
        });
    },
    loadCartItems: function () {
        $.ajax({
            url: window.appConfig.routes.cartItems,
            method: 'GET',
            success: function (response) {
                if (response.success) window.cartSystem.updateAllCartSections(response.items, response.total);
            }
        });
    },
    updateNavbarCartCount: function (count) {
        var badge = $('.cart-count');
        if (badge.length) {
            badge.text(count);
            if (count > 0) badge.show();
            else badge.hide();
        }
    },
    updateAllCartSections: function (items, total) {
        var currencySymbol = window.appConfig.currencySymbol;
        var offcanvasCartBody = $('#offcanvasCartItemsBody');
        var offcanvasCartFooter = $('#offcanvasCartFooter');
  // ✅ YEH LINE ADD - Response se currency le lo
    if (window.cartSystem.currentCurrency) {
        currencySymbol = window.cartSystem.currentCurrency;
    }

        if (offcanvasCartBody.length) {
            window.cartSystem.updateCartSection(items, total, offcanvasCartBody, offcanvasCartFooter, 'offcanvas-cart-total');
        }
        var cartPageBody = $('#cartItemsBody');
        var cartPageFooter = $('#cartFooter');
        var checkoutBtn = $('#checkoutBtn');
        if (cartPageBody.length) {
            window.cartSystem.updateCartSection(items, total, cartPageBody, cartPageFooter, 'cart-total');
            if (items && items.length > 0) checkoutBtn.removeClass('disabled');
            else checkoutBtn.addClass('disabled');
        }
    },
    updateCartSection: function (items, total, cartBody, cartFooter, totalClass) {
        var currencySymbol = window.appConfig.currencySymbol;
        if (!items || items.length === 0) {
            var emptyHtml = `<tr id="cartEmptyState"><td colspan="5" class="text-center py-10"><div class="empty-cart"><i class="ph-bold ph-shopping-bag" style="font-size: 80px; color: #ccc;"></i><h4 class="mt-4 mb-3">Your cart is empty</h4><p class="text-muted mb-6">You haven't added any products to your cart yet.</p><a href="/products" class="btn btn-dark">Start Shopping</a></div></td></tr>`;
            cartBody.html(emptyHtml);
            if (cartFooter && cartFooter.length) cartFooter.hide();
            if (totalClass === 'cart-total' || totalClass === 'offcanvas-cart-total') {
                $('.' + totalClass).text(currencySymbol + ' ' + '0.00');
            }
            if (cartBody.attr('id') === 'cartItemsBody') {
                $('#cartSubtotal').text(currencySymbol + '0.00');
                $('#cartTotal').text(currencySymbol + '0.00');
                $('#checkoutBtn').addClass('disabled');
            }
            return;
        }
        var isCartPage = cartBody.attr('id') === 'cartItemsBody';
        var itemsHtml = '';
        items.forEach(function (item) {
            var price = parseFloat(item.price) || 0;
            var quantity = parseInt(item.quantity) || 1;
            var subtotal = price * quantity;
            var variantInfo = item.variant_name ? `<br><small class="text-muted">Variant: ${item.variant_name}</small>` : '';
            var imageUrl = item.image || '';
            var storagePath = window.appConfig.siteUrl + '/storage/';
            if (imageUrl && !imageUrl.startsWith('http') && !imageUrl.startsWith('/')) {
                imageUrl = storagePath + imageUrl;
            } else if (!imageUrl) {
                imageUrl = 'https://via.placeholder.com/60x80?text=Product';
            }
            if (isCartPage) {
                itemsHtml += `<tr class="cart-item-row" id="cart-item-${item.product_id}-${item.variant_id || '0'}">
                    <td><div class="flex-align gap-16"><div class="w-60 h-60 border border-gray-100 rounded-8 flex-center p-8"><img src="${imageUrl}" alt="${item.product_name}" class="w-full h-full object-fit-cover"></div><div><h6 class="text-md mb-0"><a href="/product/${item.product_slug}" class="text-gray-900 hover-text-main-600">${item.product_name}</a></h6>${variantInfo ? `<p class="text-xs text-gray-600 mt-4">${variantInfo}</p>` : ''}</div></div></td>
                    <td><span class="text-main-600 fw-semibold product-price" data-price="${price}">${item.currency || currencySymbol} ${price.toFixed(2)}</span></td>
                    <td><div class="d-flex rounded-4 overflow-hidden"><button type="button" class="quantity__minus border border-end border-gray-100 flex-shrink-0 h-48 w-48 text-neutral-600 flex-center hover-bg-main-600 hover-text-white cart-qty-down" data-product-id="${item.product_id}" data-variant-id="${item.variant_id || ''}"><i class="ph ph-minus"></i></button><input type="number" class="quantity__input flex-grow-1 border border-gray-100 border-start-0 border-end-0 text-center w-32 px-4 cart-qty-input" value="${quantity}" min="1" data-product-id="${item.product_id}" data-variant-id="${item.variant_id || ''}"><button type="button" class="quantity__plus border border-end border-gray-100 flex-shrink-0 h-48 w-48 text-neutral-600 flex-center hover-bg-main-600 hover-text-white cart-qty-up" data-product-id="${item.product_id}" data-variant-id="${item.variant_id || ''}"><i class="ph ph-plus"></i></button></div></td>
                    <td><span class="text-main-600 fw-semibold item-subtotal">${currencySymbol} ${subtotal.toFixed(2)}</span></td>
                    <td><button type="button" class="remove-tr-btn flex-align gap-12 hover-text-danger-600 remove-cart-item" data-product-id="${item.product_id}" data-variant-id="${item.variant_id || ''}"><i class="ph-bold ph-trash fs-5"></i></button></td>
                </tr>`;
            } else {
                itemsHtml += `<tr class="cart-item-row" id="cart-item-${item.product_id}-${item.variant_id || '0'}">
                    <td class="align-middle text-center"><a href="javascript:void(0)" class="d-block clear-product remove-cart-item text-danger" data-product-id="${item.product_id}" data-variant-id="${item.variant_id || ''}" title="Remove from cart"><i class="ph-bold ph-x"></i></a></td>
                    <td class="shop-product"><div class="d-flex align-items-center"><div class="me-3"><img src="${imageUrl}" width="60" height="80" alt="${item.product_name}" style="object-fit: cover; border-radius: 4px;" onerror="this.src='https://via.placeholder.com/60x80?text=Product'" class="cart-item-image"></div><div><p class="fw-500 text-body-emphasis mb-1">${item.product_name}${variantInfo}</p><p class="card-text mb-0"><span class="fs-15px fw-bold text-body-emphasis">${item.currency || currencySymbol} ${price.toFixed(2)}</span></p></div></div></td>
                    <td class="align-middle"><div class="d-flex align-items-center justify-content-center"><a href="javascript:void(0)" class="cart-qty-down text-decoration-none" data-product-id="${item.product_id}" data-variant-id="${item.variant_id || ''}" style="padding: 5px 10px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px 0 0 4px;"><i class="ph-bold ph-minus"></i></a><input type="number" class="form-control form-control-sm text-center cart-qty-input" value="${quantity}" min="1" data-product-id="${item.product_id}" data-variant-id="${item.variant_id || ''}" style="width: 50px; border-radius: 0; border-left: 0; border-right: 0;"><a href="javascript:void(0)" class="cart-qty-up text-decoration-none" data-product-id="${item.product_id}" data-variant-id="${item.variant_id || ''}" style="padding: 5px 10px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 0 4px 4px 0;"><i class="ph-bold ph-plus"></i></a></div></td>
                </tr>`;
            }
        });
        var totalNum = parseFloat(total) || 0;
        cartBody.html(itemsHtml);
        if (isCartPage) {
            $('#cartSubtotal').text(currencySymbol + totalNum.toFixed(2));
            $('#cartTotal').text(currencySymbol + totalNum.toFixed(2));
            $('#checkoutBtn').removeClass('disabled');
        } else {
            $('.' + totalClass).text(currencySymbol + ' ' + totalNum.toFixed(2));
        }
        if (cartFooter && cartFooter.length) cartFooter.show();
    }
};

$(document).ready(function () {
    window.cartSystem.loadCartCount();
    $('#shoppingCart').on('show.bs.offcanvas', function () {
        window.cartSystem.loadCartItems();
    });
    $(document).on('click', '.add_to_cart', function (e) {
        e.preventDefault();
        if ($(this).hasClass('disabled') || $(this).prop('disabled') || $(this).hasClass('opacity-50')) return false;
        var productId = $(this).data('product-id');
        var button = $(this);
        var originalHtml = button.html();
        if (!productId || productId === 'undefined') return false;
        button.html('<span class="spinner-border spinner-border-sm me-2"></span>');
        button.prop('disabled', true);
        var quantity = 1;
        var variantId = null;
        $.ajax({
            url: window.appConfig.routes.cartAdd,
            method: 'POST',
            dataType: 'json',
            data: { product_id: productId, variant_id: variantId, quantity: quantity, _token: window.appConfig.csrfToken },
            success: function (response) {
                if (response.success) {
                    window.cartSystem.updateNavbarCartCount(response.count);
                    window.cartSystem.updateAllCartSections(response.items, response.total);
                    var offcanvas = new bootstrap.Offcanvas('#shoppingCart');
                    offcanvas.show();
                    button.addClass('bg-success text-white').html('<i class="fas fa-check"></i>');
                    setTimeout(function () { button.html(originalHtml).removeClass('bg-success text-white'); }, 1000);
                } else {
                    button.html(originalHtml).removeClass('bg-success text-white');
                }
            },
            error: function () { button.html(originalHtml); },
            complete: function () { setTimeout(function () { button.prop('disabled', false); }, 500); }
        });
    });
    $(document).on('click', '.remove-cart-item', function (e) {
        e.preventDefault();
        var productId = $(this).data('product-id');
        var variantId = $(this).data('variant-id') || '';
        var row = $(this).closest('tr');
        removeCartItem(productId, variantId, row);
    });
    function removeCartItem(productId, variantId, row) {
        $.ajax({
            url: window.appConfig.routes.cartRemove,
            method: 'POST',
            dataType: 'json',
            data: { product_id: productId, variant_id: variantId || null, _token: window.appConfig.csrfToken },
            success: function (response) {
                if (response.success) {
                    $('.cart-item-row#cart-item-' + productId + '-' + (variantId || '0')).fadeOut(300, function () {
                        $(this).remove();
                        var remainingItems = $('.cart-item-row').length;
                        if (remainingItems === 0) {
                            var emptyHtml = `<tr id="cartEmptyState"><td colspan="3" class="text-center py-10"><div class="empty-cart"><svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#ccc"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg><h4 class="mt-4 mb-3">Your cart is empty</h4><p class="text-muted mb-6">You haven't added any products to your cart yet.</p><a href="/products" class="btn btn-dark">Start Shopping</a></div></td></tr>`;
                            if ($('#offcanvasCartItemsBody').length) {
                                $('#offcanvasCartItemsBody').html(emptyHtml);
                                $('#offcanvasCartFooter').hide();
                                $('.offcanvas-cart-total').text(window.appConfig.currencySymbol + ' ' + '0.00');
                            }
                            if ($('#cartItemsBody').length) {
                                $('#cartItemsBody').html(emptyHtml);
                                $('#cartFooter').hide();
                                $('#checkoutBtn').addClass('disabled');
                                $('.cart-total').text(window.appConfig.currencySymbol + ' ' + '0.00');
                            }
                        } else {
                            var totalNum = parseFloat(response.total) || 0;
                            $('.cart-total, .offcanvas-cart-total').text(window.appConfig.currencySymbol + ' ' + totalNum.toFixed(2));
                        }
                        window.cartSystem.updateNavbarCartCount(response.count);
                    });
                }
            }
        });
    }
    $(document).on('click', '.cart-qty-up', function (e) {
        e.preventDefault();
        var productId = $(this).data('product-id');
        var variantId = $(this).data('variant-id') || '';
        var input = $(this).closest('.d-flex').find('.cart-qty-input');
        var currentVal = parseInt(input.val()) || 1;
        var newQuantity = currentVal + 1;
        input.val(newQuantity);
        updateCartQuantity(productId, variantId, newQuantity);
    });
    $(document).on('click', '.cart-qty-down', function (e) {
        e.preventDefault();
        var productId = $(this).data('product-id');
        var variantId = $(this).data('variant-id') || '';
        var input = $(this).closest('.d-flex').find('.cart-qty-input');
        var currentVal = parseInt(input.val()) || 1;
        if (currentVal > 1) {
            var newQuantity = currentVal - 1;
            input.val(newQuantity);
            updateCartQuantity(productId, variantId, newQuantity);
        } else {
            var row = $(this).closest('tr');
            removeCartItem(productId, variantId, row);
        }
    });
    $(document).on('change', '.cart-qty-input', function () {
        var productId = $(this).data('product-id');
        var variantId = $(this).data('variant-id') || '';
        var newVal = parseInt($(this).val()) || 1;
        if (newVal < 1) { $(this).val(1); newVal = 1; }
        updateCartQuantity(productId, variantId, newVal);
    });
    function updateCartQuantity(productId, variantId, quantity) {
        var input = $(`.cart-qty-input[data-product-id="${productId}"][data-variant-id="${variantId || ''}"]`);
        var oldValue = input.val();
        input.prop('disabled', true);
        $.ajax({
            url: window.appConfig.routes.cartUpdate,
            method: 'POST',
            data: { product_id: productId, variant_id: variantId || null, quantity: quantity, _token: window.appConfig.csrfToken },
            success: function (response) {
                if (response.success) {
                    var totalNum = parseFloat(response.total) || 0;
                    $('.cart-total, .offcanvas-cart-total').text(window.appConfig.currencySymbol + ' ' + totalNum.toFixed(2));
                    window.cartSystem.updateNavbarCartCount(response.count);
                } else { input.val(oldValue); }
            },
            error: function () { input.val(oldValue); },
            complete: function () { input.prop('disabled', false); }
        });
    }
    $(document).on('click', '#clearCartBtn', function (e) {
        e.preventDefault();
        $.ajax({
            url: window.appConfig.routes.cartClear,
            method: 'POST',
            data: { _token: window.appConfig.csrfToken },
            success: function (response) {
                if (response.success) {
                    window.cartSystem.updateNavbarCartCount(0);
                    window.cartSystem.updateAllCartSections([], 0);
                }
            }
        });
    });
    if (window.location.pathname.includes('/cart')) window.cartSystem.loadCartItems();
});

// ========== COUPON & SHIPPING FUNCTIONS ==========
$(document).ready(function () {
    console.log('Auto Shipping & Coupon System Loaded');
    var currencySymbol = window.appConfig.currencySymbol;
    var currentDiscount = 0;
    var appliedCouponCode = '';

    function loadAppliedCouponOnCartPage() {
        $.ajax({
            url: window.appConfig.routes.cartCouponStatus,
            method: 'GET',
            success: function (response) {
                if (response.success && response.applied) {
                    currentDiscount = response.discount_amount;
                    appliedCouponCode = response.coupon_code;
                    $('#couponSummaryRow').show();
                    $('#summaryDiscount').text('-' + currencySymbol + ' ' + currentDiscount.toFixed(2));
                    $('#applyCouponBtn').hide();
                    $('#removeCouponBtn').show();
                    $('#couponCodeInput').val(appliedCouponCode).prop('disabled', true);
                    $('#couponMessage').html('<div class="alert alert-success">Coupon ' + appliedCouponCode + ' is applied</div>');
                    validateAppliedCoupon();
                } else { resetCouponVariables(); }
            },
            error: function () { resetCouponVariables(); }
        });
    }

    function validateAppliedCoupon() {
        if (!appliedCouponCode || currentDiscount === 0) return;
        $.ajax({
            url: window.appConfig.routes.cartTotalCalc,
            method: 'GET',
            success: function (response) {
                if (response.success) {
                    var cartTotal = response.cart_total;
                    $.ajax({
                        url: window.appConfig.routes.cartValidateCoupon,
                        method: 'POST',
                        data: { coupon_code: appliedCouponCode, cart_total: cartTotal, _token: window.appConfig.csrfToken },
                        success: function (validationResponse) {
                            if (!validationResponse.success || !validationResponse.valid) {
                                removeInvalidCoupon(validationResponse.message);
                            } else {
                                if (validationResponse.discount_amount !== currentDiscount) {
                                    currentDiscount = validationResponse.discount_amount;
                                    $('#summaryDiscount').text('-' + currencySymbol + ' ' + currentDiscount.toFixed(2));
                                    $('#couponMessage').html('<div class="alert alert-success">Coupon updated. New discount: ' + currencySymbol + ' ' + currentDiscount.toFixed(2) + '</div>');
                                    setTimeout(function () { calculateShippingAutomatically(); }, 300);
                                }
                            }
                        }
                    });
                }
            }
        });
    }

    function removeInvalidCoupon(message) {
        resetCouponVariables();
        $('#summaryDiscount').text('-' + currencySymbol + ' ' + '0.00');
        $('#couponSummaryRow').hide();
        $('#applyCouponBtn').show();
        $('#removeCouponBtn').hide();
        $('#couponCodeInput').val('').prop('disabled', false);
        $('#couponMessage').html('<div class="alert alert-danger">' + (message || 'Coupon is no longer valid') + '</div>');
        $.ajax({ url: window.appConfig.routes.cartRemoveCoupon, method: 'POST', data: { _token: window.appConfig.csrfToken } });
        setTimeout(function () { calculateShippingAutomatically(); }, 300);
    }

    function resetCouponVariables() { currentDiscount = 0; appliedCouponCode = ''; }

    function calculateShippingAutomatically() {
        $.ajax({
            url: window.appConfig.routes.cartTotalCalc,
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success && response.cart_total > 0) {
                    var cartTotal = response.cart_total;
                    $.ajax({
                        url: window.appConfig.routes.cartShippingCalc,
                        method: 'POST',
                        dataType: 'json',
                        data: { cart_total: cartTotal, _token: window.appConfig.csrfToken },
                        success: function (shippingResponse) {
                            if (shippingResponse.success) updateShippingUI(shippingResponse, cartTotal);
                        }
                    });
                } else {
                    $('#shippingSummaryRow').hide();
                    $('#autoShippingMessage').html('');
                    $('#summaryShipping').text(currencySymbol + ' ' + '0.00');
                }
            }
        });
    }

 function updateShippingUI(shippingResponse, cartTotal) {
    var shippingFee = shippingResponse.shipping_fee;
    var maxOrderAmount = shippingResponse.max_order_amount;
    var remainingAmount = shippingResponse.remaining_amount; // ✅ YEH USE KAR
    
    $('#shippingSummaryRow').show();
    
    if (shippingFee === 0) {
        $('#summaryShipping').text('Free');
        $('#shippingMessageContainer').html('<div class="alert alert-success p-2 mt-2 mb-0 small"><i class="ph ph-truck"></i> Free Shipping Applied!</div>');
    } else {
        $('#summaryShipping').text(currencySymbol + ' ' + shippingFee.toFixed(2));
        
        if (maxOrderAmount && remainingAmount > 0) {
            $('#shippingMessageContainer').html('<div class="alert alert-info p-2 mt-2 mb-0 small"><i class="ph ph-truck"></i> Add ' + currencySymbol + ' ' + remainingAmount.toFixed(2) + ' more for free shipping!</div>');
        } else { 
            $('#shippingMessageContainer').html(''); 
        }
    }
    updateOrderSummary(cartTotal);
}

    function updateOrderSummary(cartSubtotal) {
        var shipping = parseFloat($('#summaryShipping').text().replace(currencySymbol, '')) || 0;
        var total = cartSubtotal + shipping - currentDiscount;
        $('#summarySubtotal').text(currencySymbol + ' ' + cartSubtotal.toFixed(2));
        $('#summaryTotal').text(currencySymbol + ' ' + total.toFixed(2));
        if (total > 0) $('#checkoutBtn').removeClass('disabled');
        else $('#checkoutBtn').addClass('disabled');
    }

    function handleCartChange() {
        if (appliedCouponCode) validateAppliedCoupon();
        setTimeout(function () { calculateShippingAutomatically(); }, 800);
    }

    $(document).on('cartUpdated', function () { handleCartChange(); });
    $(document).on('change', '.cart-qty-input', function () { setTimeout(function () { handleCartChange(); }, 1000); });
    $(document).on('click', '.cart-qty-up, .cart-qty-down', function () { setTimeout(function () { handleCartChange(); }, 1000); });
    $(document).on('click', '.remove-cart-item', function () { setTimeout(function () { handleCartChange(); }, 1000); });

    $('#applyCouponBtn').on('click', function () {
        var couponCode = $('#couponCodeInput').val().trim();
        if (!couponCode) { $('#couponMessage').html('<div class="alert alert-warning">Please enter coupon code</div>'); return; }
        $.ajax({
            url: window.appConfig.routes.cartTotalCalc,
            method: 'GET',
            success: function (response) {
                if (response.success) {
                    var cartTotal = response.cart_total;
                    $.ajax({
                        url: window.appConfig.routes.cartApplyCoupon,
                        method: 'POST',
                        dataType: 'json',
                        data: { coupon_code: couponCode, cart_total: cartTotal, _token: window.appConfig.csrfToken },
                        success: function (couponResponse) {
                            if (couponResponse.success) {
                                currentDiscount = couponResponse.discount_amount;
                                appliedCouponCode = couponResponse.coupon_code;
                                $('#couponMessage').html('<div class="alert alert-success">' + couponResponse.message + '</div>');
                                $('#couponSummaryRow').show();
                                $('#summaryDiscount').text('-' + currencySymbol + ' ' + currentDiscount.toFixed(2));
                                $('#applyCouponBtn').hide();
                                $('#removeCouponBtn').show();
                                $('#couponCodeInput').prop('disabled', true);
                                setTimeout(function () { calculateShippingAutomatically(); }, 500);
                            } else { $('#couponMessage').html('<div class="alert alert-danger">' + couponResponse.message + '</div>'); }
                        }
                    });
                }
            }
        });
    });

    $(document).on('click', '#removeCouponBtn', function () { removeInvalidCoupon('Coupon removed by user'); });
    loadAppliedCouponOnCartPage();
    setTimeout(function () { calculateShippingAutomatically(); }, 500);
});

// ========== WISHLIST FUNCTIONS ==========
window.wishlistFunctions = {
    updateWishlistCount: function (count) {
        var badge = $('.wishlist-count');
        if (badge.length) { badge.text(count); if (count > 0) badge.show(); else badge.hide(); }
    },
    loadFreshWishlistCount: function () {
        $.ajax({ url: window.appConfig.routes.wishlistCount, type: "GET", success: function (response) { if (response.success) window.wishlistFunctions.updateWishlistCount(response.count); } });
    },
    showWishlistToast: function (message, type = 'success') {
        if (typeof Swal !== 'undefined') Swal.fire({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2000, icon: type, title: message });
        else alert(message);
    },
    updateButtonIcon: function (button, inWishlist) {
        var phIcon = button.find('i.ph-heart, i.ph-bold.ph-heart');
        if (phIcon.length) {
            if (inWishlist) phIcon.removeClass('ph-heart ph-bold').addClass('ph-fill ph-heart text-danger');
            else phIcon.removeClass('ph-fill text-danger').addClass('ph-bold ph-heart');
        }
        if (inWishlist) { button.addClass('active text-warning'); button.attr('data-bs-title', 'Remove from Wishlist'); }
        else { button.removeClass('active text-warning'); button.attr('data-bs-title', 'Add to Wishlist'); }
        var textSpan = button.find('span:contains("wishlist")');
        if (textSpan.length) textSpan.text(inWishlist ? 'Remove from wishlist' : 'Add to wishlist');
        if (button.attr('data-bs-toggle') === 'tooltip') { var tooltip = bootstrap.Tooltip.getInstance(button[0]); if (tooltip) tooltip.dispose(); new bootstrap.Tooltip(button[0]); }
    },
    checkProductWishlistStatus: function (productId, button) {
        $.ajax({ url: window.appConfig.routes.wishlistCheck, type: "POST", data: { product_id: productId, _token: window.appConfig.csrfToken }, success: function (response) { if (response.success) window.wishlistFunctions.updateButtonIcon(button, response.in_wishlist); } });
    },
    checkAllProductsWishlistStatus: function () { $('.wishlist-toggle').each(function () { var productId = $(this).data('product-id'); var button = $(this); if (!productId) return; setTimeout(function () { window.wishlistFunctions.checkProductWishlistStatus(productId, button); }, 100); }); },
    checkProductDetailsWishlistStatus: function () { var detailsButton = $('.wishlist-toggle').first(); if (detailsButton.length) { var productId = detailsButton.data('product-id'); if (productId) window.wishlistFunctions.checkProductWishlistStatus(productId, detailsButton); } }
};

$(document).ready(function () {
    window.wishlistFunctions.loadFreshWishlistCount();
    window.wishlistFunctions.checkAllProductsWishlistStatus();
    if (window.location.pathname.includes('/products/')) window.wishlistFunctions.checkProductDetailsWishlistStatus();
    $(document).on('click', '.wishlist-toggle', function (e) {
        e.preventDefault();
        var button = $(this);
        var productId = button.data('product-id');
        if (!productId) return;
        button.prop('disabled', true);
        $.ajax({
            url: window.appConfig.routes.wishlistToggle,
            type: "POST",
            data: { product_id: productId, _token: window.appConfig.csrfToken },
            success: function (response) {
                if (response.success) {
                    window.wishlistFunctions.updateButtonIcon(button, response.in_wishlist);
                    if (response.count && response.count.original && response.count.original.count !== undefined) window.wishlistFunctions.updateWishlistCount(response.count.original.count);
                    else if (response.count !== undefined) window.wishlistFunctions.updateWishlistCount(response.count);
                    else window.wishlistFunctions.loadFreshWishlistCount();
                    window.wishlistFunctions.showWishlistToast(response.message, 'success');
                } else window.wishlistFunctions.showWishlistToast(response.message, 'error');
                button.prop('disabled', false);
            },
            error: function () { window.wishlistFunctions.showWishlistToast('Network error!', 'error'); button.prop('disabled', false); }
        });
    });
    $(document).on('click', '.remove-wishlist', function (e) {
        e.preventDefault();
        var button = $(this);
        var productId = button.data('product-id');
        if (!productId) return;
        var row = $('#wishlist-row-' + productId);
        $.ajax({
            url: window.appConfig.routes.wishlistRemove,
            type: "POST",
            data: { product_id: productId, _token: window.appConfig.csrfToken },
            success: function (response) {
                if (response.success) {
                    row.fadeOut(300, function () { $(this).remove(); });
                    if (response.count && response.count.original && response.count.original.count !== undefined) window.wishlistFunctions.updateWishlistCount(response.count.original.count);
                    else if (response.count !== undefined) window.wishlistFunctions.updateWishlistCount(response.count);
                    else window.wishlistFunctions.loadFreshWishlistCount();
                    $(`.wishlist-toggle[data-product-id="${productId}"]`).each(function () { window.wishlistFunctions.updateButtonIcon($(this), false); });
                    window.wishlistFunctions.showWishlistToast(response.message, 'success');
                } else window.wishlistFunctions.showWishlistToast(response.message, 'error');
            },
            error: function () { window.wishlistFunctions.showWishlistToast('Something went wrong!', 'error'); }
        });
    });
});
$(window).on('load', function () { setTimeout(function () { window.wishlistFunctions.loadFreshWishlistCount(); }, 500); });

// ========== COMPARE FUNCTIONS ==========
window.compareFunctions = {
    updateCompareCount: function (count) { var badge = $('.compare-count'); if (badge.length) { if (count > 0) badge.text(count).show(); else badge.hide(); } },
    loadFreshCompareCount: function () { $.ajax({ url: window.appConfig.routes.compareCount, type: "GET", success: function (response) { if (response.success) window.compareFunctions.updateCompareCount(response.count); } }); },
    showCompareToast: function (message, type = 'success') { if (typeof Swal !== 'undefined') Swal.fire({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2000, icon: type, title: message }); else alert(message); },
    updateButtonIcon: function (button, inCompare) { var phIcon = button.find('i.ph-recycle, i.ph-bold.ph-recycle, i.ph-fill.ph-recycle, i.ph-shuffle'); if (phIcon.length) { if (inCompare) phIcon.removeClass('ph-bold ph-shuffle').addClass('ph-fill text-main-600'); else phIcon.removeClass('ph-fill text-main-600').addClass('ph-bold ph-shuffle'); } if (inCompare) { button.addClass('active text-main-600'); button.attr('data-bs-title', 'Remove from Compare'); button.attr('title', 'Remove from Compare'); } else { button.removeClass('active text-main-600'); button.attr('data-bs-title', 'Add to Compare'); button.attr('title', 'Add to Compare'); } if (button.attr('data-bs-toggle') === 'tooltip') { var tooltip = bootstrap.Tooltip.getInstance(button[0]); if (tooltip) tooltip.dispose(); new bootstrap.Tooltip(button[0]); } },
    checkProductCompareStatus: function (productId, button) { $.ajax({ url: window.appConfig.routes.compareCheck, type: "POST", data: { product_id: productId, _token: window.appConfig.csrfToken }, success: function (response) { if (response.success) window.compareFunctions.updateButtonIcon(button, response.in_compare); } }); },
    checkAllProductsCompareStatus: function () { $('.compare-toggle').each(function () { var productId = $(this).data('product-id'); var button = $(this); if (!productId) return; setTimeout(function () { window.compareFunctions.checkProductCompareStatus(productId, button); }, 100); }); },
    removeProductColumn: function (productId) { $('.compare-product-column[data-product-id="' + productId + '"]').remove(); $('.price-cell[data-product-id="' + productId + '"]').remove(); $('.stock-cell[data-product-id="' + productId + '"]').remove(); $('.sku-cell[data-product-id="' + productId + '"]').remove(); $('.brand-cell[data-product-id="' + productId + '"]').remove(); $('.category-cell[data-product-id="' + productId + '"]').remove(); $('.vendor-cell[data-product-id="' + productId + '"]').remove(); $('.rating-cell[data-product-id="' + productId + '"]').remove(); $('.description-cell[data-product-id="' + productId + '"]').remove(); $('.action-cell[data-product-id="' + productId + '"]').remove(); var remainingColumns = $('.compare-product-column').length; if (remainingColumns === 0) { setTimeout(function () { location.reload(); }, 1000); } }
};

$(document).ready(function () {
    window.compareFunctions.loadFreshCompareCount();
    window.compareFunctions.checkAllProductsCompareStatus();
    $(document).on('click', '.compare-toggle', function (e) {
        e.preventDefault();
        var button = $(this);
        var productId = button.data('product-id');
        if (!productId) return;
        button.prop('disabled', true);
        $.ajax({
            url: window.appConfig.routes.compareToggle,
            type: "POST",
            data: { product_id: productId, _token: window.appConfig.csrfToken },
            success: function (response) {
                if (response.success) {
                    window.compareFunctions.updateButtonIcon(button, response.in_compare);
                    if (response.count && response.count.original && response.count.original.count !== undefined) window.compareFunctions.updateCompareCount(response.count.original.count);
                    else if (response.count !== undefined) window.compareFunctions.updateCompareCount(response.count);
                    else window.compareFunctions.loadFreshCompareCount();
                    window.compareFunctions.showCompareToast(response.message, 'success');
                    if (window.location.pathname.includes('/compare') && !response.in_compare) window.compareFunctions.removeProductColumn(productId);
                } else window.compareFunctions.showCompareToast(response.message, 'error');
                button.prop('disabled', false);
            },
            error: function () { window.compareFunctions.showCompareToast('Network error!', 'error'); button.prop('disabled', false); }
        });
    });
    $(document).on('click', '.remove-compare', function (e) {
        e.preventDefault();
        var button = $(this);
        var productId = button.data('product-id');
        if (!productId) return;
        $.ajax({
            url: window.appConfig.routes.compareRemove,
            type: "POST",
            data: { product_id: productId, _token: window.appConfig.csrfToken },
            success: function (response) {
                if (response.success) {
                    window.compareFunctions.showCompareToast(response.message, 'success');
                    if (response.count && response.count.original && response.count.original.count !== undefined) window.compareFunctions.updateCompareCount(response.count.original.count);
                    else if (response.count !== undefined) window.compareFunctions.updateCompareCount(response.count);
                    else window.compareFunctions.loadFreshCompareCount();
                    window.compareFunctions.removeProductColumn(productId);
                    $(`.compare-toggle[data-product-id="${productId}"]`).each(function () { window.compareFunctions.updateButtonIcon($(this), false); });
                } else window.compareFunctions.showCompareToast(response.message, 'error');
            }
        });
    });
    $(document).on('click', '#clear-all-compare', function (e) {
        e.preventDefault();
        if (typeof Swal !== 'undefined') {
            Swal.fire({ title: 'Clear Compare List?', text: "All products will be removed from compare list", icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6', confirmButtonText: 'Yes, clear all!' }).then((result) => {
                if (result.isConfirmed) {
                    var productIds = []; $('.remove-compare').each(function () { productIds.push($(this).data('product-id')); });
                    if (productIds.length === 0) return;
                    var removed = 0;
                    productIds.forEach(function (id) {
                        $.ajax({ url: window.appConfig.routes.compareRemove, type: "POST", data: { product_id: id, _token: window.appConfig.csrfToken }, success: function (response) { removed++; window.compareFunctions.removeProductColumn(id); if (removed === productIds.length) { window.compareFunctions.updateCompareCount(0); window.compareFunctions.showCompareToast('Compare list cleared', 'success'); setTimeout(function () { location.reload(); }, 1000); } } });
                    });
                }
            });
        } else {
            if (confirm('Clear all products from compare list?')) {
                var productIds = []; $('.remove-compare').each(function () { productIds.push($(this).data('product-id')); });
                if (productIds.length === 0) return;
                var removed = 0;
                productIds.forEach(function (id) {
                    $.ajax({ url: window.appConfig.routes.compareRemove, type: "POST", data: { product_id: id, _token: window.appConfig.csrfToken }, success: function () { removed++; window.compareFunctions.removeProductColumn(id); if (removed === productIds.length) { window.compareFunctions.updateCompareCount(0); setTimeout(function () { location.reload(); }, 1000); } } });
                });
            }
        }
    });
});
$(window).on('load', function () { setTimeout(function () { window.compareFunctions.loadFreshCompareCount(); }, 500); });

// ========== CONTACT FUNCTIONS ==========
window.contactFunctions = {
    showToast: function (message, type = 'success') { if (typeof Swal !== 'undefined') Swal.fire({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true, icon: type, title: message, didOpen: (toast) => { toast.addEventListener('mouseenter', Swal.stopTimer); toast.addEventListener('mouseleave', Swal.resumeTimer); } }); else if (typeof toastr !== 'undefined') toastr[type](message); else alert(message); },
    resetForm: function (form) { form[0].reset(); $('[id$="-error"]').text(''); },
    setLoading: function (button, isLoading) { var btnText = button.find('.btn-text'); var spinner = button.find('.spinner-border'); button.prop('disabled', isLoading); if (isLoading) { btnText.addClass('d-none'); spinner.removeClass('d-none'); } else { btnText.removeClass('d-none'); spinner.addClass('d-none'); } },
    displayErrors: function (errors) { $.each(errors, function (field, messages) { $(`#${field}-error`).text(messages[0]); }); },
    clearErrors: function () { $('[id$="-error"]').text(''); }
};

$(document).ready(function () {
    var contactForm = $('#contactForm');
    var submitBtn = $('#submitBtn');
    contactForm.on('submit', function (e) {
        e.preventDefault();
        window.contactFunctions.clearErrors();
        window.contactFunctions.setLoading(submitBtn, true);
        var formData = new FormData(this);
        $.ajax({
            url: window.appConfig.routes.contactSubmit,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': window.appConfig.csrfToken },
            success: function (response) { if (response.success) { window.contactFunctions.showToast(response.message, 'success'); window.contactFunctions.resetForm(contactForm); } },
            error: function (xhr) { if (xhr.status === 422) { var errors = xhr.responseJSON.errors; window.contactFunctions.displayErrors(errors); window.contactFunctions.showToast('Please check the form for errors', 'error'); } else { var message = xhr.responseJSON?.message || 'Something went wrong. Please try again.'; window.contactFunctions.showToast(message, 'error'); } },
            complete: function () { window.contactFunctions.setLoading(submitBtn, false); }
        });
    });
});

// ========== NEWSLETTER FUNCTIONS ==========
window.newsletterFunctions = {
    showToast: function (message, type = 'success') { if (typeof Swal !== 'undefined') Swal.fire({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true, icon: type, title: message, didOpen: (toast) => { toast.addEventListener('mouseenter', Swal.stopTimer); toast.addEventListener('mouseleave', Swal.resumeTimer); } }); else if (typeof toastr !== 'undefined') toastr[type](message); else alert(message); },
    setLoading: function (button, isLoading) { var btnText = button.find('.btn-text'); var spinner = button.find('.spinner-border'); button.prop('disabled', isLoading); if (isLoading) { btnText.addClass('d-none'); spinner.removeClass('d-none'); } else { btnText.removeClass('d-none'); spinner.addClass('d-none'); } },
    clearError: function () { $('#newsletter-error').text(''); },
    setError: function (message) { $('#newsletter-error').text(message); },
    resetForm: function (form) { form[0].reset(); }
};

$(document).ready(function () {
    var newsletterForm = $('#newsletterForm');
    var subscribeBtn = $('#subscribeBtn');
    newsletterForm.on('submit', function (e) {
        e.preventDefault();
        window.newsletterFunctions.clearError();
        var email = $('#newsletter_email').val();
        if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { window.newsletterFunctions.setError('Please enter a valid email address'); return; }
        window.newsletterFunctions.setLoading(subscribeBtn, true);
        var formData = new FormData(this);
        $.ajax({
            url: window.appConfig.routes.newsletterSubscribe,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': window.appConfig.csrfToken },
            success: function (response) { if (response.success) { window.newsletterFunctions.showToast(response.message, 'success'); window.newsletterFunctions.resetForm(newsletterForm); } },
            error: function (xhr) { if (xhr.status === 422) { var errors = xhr.responseJSON.errors; if (errors && errors.email) window.newsletterFunctions.setError(errors.email[0]); else window.newsletterFunctions.setError('Please enter a valid email address'); } else if (xhr.status === 409) { var message = xhr.responseJSON?.message || 'This email is already subscribed'; window.newsletterFunctions.showToast(message, 'info'); } else { var message = xhr.responseJSON?.message || 'Something went wrong. Please try again.'; window.newsletterFunctions.showToast(message, 'error'); } },
            complete: function () { window.newsletterFunctions.setLoading(subscribeBtn, false); }
        });
    });
    $(document).on('click', '.unsubscribe-link', function (e) {
        e.preventDefault();
        var email = $(this).data('email');
        if (!email) return;
        if (confirm('Are you sure you want to unsubscribe from our newsletter?')) {
            $.ajax({ url: window.appConfig.routes.newsletterSubscribe, type: 'POST', data: { email: email, _token: window.appConfig.csrfToken }, success: function (response) { window.newsletterFunctions.showToast(response.message, 'success'); }, error: function (xhr) { var message = xhr.responseJSON?.message || 'Something went wrong'; window.newsletterFunctions.showToast(message, 'error'); } });
        }
    });
});

// ========== TRENDING PRODUCTS LOAD MORE ==========
document.addEventListener('DOMContentLoaded', function () {
    let currentPage = 1;
    let loading = false;
    let hasMore = false;
    var viewMoreButton = document.getElementById('view-more-trending');
    var container = document.getElementById('trending-products-container');
    var buttonContainer = document.getElementById('trending-button-container');
    var loadingIndicator = document.getElementById('trending-loading');
    if (viewMoreButton) {
        viewMoreButton.addEventListener('click', function () { if (loading || !hasMore) return; loadMoreTrendingProducts(); });
    }
    function loadMoreTrendingProducts() {
        loading = true;
        currentPage++;
        if (loadingIndicator) loadingIndicator.style.display = 'block';
        if (viewMoreButton) { viewMoreButton.disabled = true; viewMoreButton.innerHTML = 'Loading... <i class="ph ph-arrow-right ms-2"></i>'; }
        fetch('/trending-products?page=' + currentPage, { method: 'GET', headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
            .then(response => response.json())
            .then(data => {
                if (data.html && data.html.trim() !== '') {
                    container.insertAdjacentHTML('beforeend', data.html);
                    hasMore = data.has_more;
                    if (!hasMore && viewMoreButton) {
                        viewMoreButton.remove();
                        buttonContainer.innerHTML = `<a href="/products" class="fw-medium text-main-600 py-14 px-24 bg-main-50 rounded-pill line-height-1 hover-bg-main-600 hover-text-white">Shop Now <i class="ph ph-arrow-right ms-2"></i></a>`;
                    }
                } else {
                    hasMore = false;
                    if (viewMoreButton) viewMoreButton.remove();
                    buttonContainer.innerHTML = `<a href="/products" class="fw-medium text-main-600 py-14 px-24 bg-main-50 rounded-pill line-height-1 hover-bg-main-600 hover-text-white">Shop Now <i class="ph ph-arrow-right ms-2"></i></a>`;
                }
            })
            .catch(error => { currentPage--; })
            .finally(() => {
                loading = false;
                if (loadingIndicator) loadingIndicator.style.display = 'none';
                if (viewMoreButton && hasMore) { viewMoreButton.disabled = false; viewMoreButton.innerHTML = 'View More <i class="ph ph-arrow-right ms-2"></i>'; }
            });
    }
});

// ========== WEB SEARCH FUNCTION ==========
document.addEventListener('DOMContentLoaded', function () {
    var input = document.getElementById('web-search-input');
    var resultsDiv = document.getElementById('web-search-results');
    var contentDiv = document.getElementById('web-search-content');
    var loadingDiv = document.getElementById('web-search-loading');
    var timeout = null;
    if (!input) return;
    input.addEventListener('input', function () {
        var query = this.value.trim();
        if (timeout) clearTimeout(timeout);
        if (query.length < 2) { resultsDiv.style.display = 'none'; return; }
        loadingDiv.style.display = 'block';
        contentDiv.innerHTML = '';
        resultsDiv.style.display = 'block';
        timeout = setTimeout(() => {
            fetch('/web-search?q=' + encodeURIComponent(query))
                .then(res => res.json())
                .then(data => {
                    loadingDiv.style.display = 'none';
                    if (!data.products || data.products.length === 0) { contentDiv.innerHTML = '<div class="text-center p-4 text-muted">No products found</div>'; return; }
                    var html = '';
                    data.products.forEach(p => {
                        html += `<div class="web-search-item" onclick="window.location.href='/product/${p.product_slug}'"><img src="${p.thumbnail_image ? '/storage/' + p.thumbnail_image : '/default-image.jpg'}" class="web-search-img" onerror="this.src='/default-image.jpg'"><div class="web-search-info"><div class="web-search-title">${escapeHtml(p.product_name)}</div><div class="web-search-price">Rs. ${parseFloat(p.product_price).toFixed(2)}</div></div></div>`;
                    });
                    contentDiv.innerHTML = html;
                })
                .catch(() => { loadingDiv.style.display = 'none'; contentDiv.innerHTML = '<div class="text-center p-4 text-muted">Something went wrong</div>'; });
        }, 300);
    });
    function escapeHtml(str) { if (!str) return ''; return str.replace(/[&<>]/g, function (m) { if (m === '&') return '&amp;'; if (m === '<') return '&lt;'; if (m === '>') return '&gt;'; return m; }); }
    document.addEventListener('click', function (e) { if (!input.contains(e.target) && !resultsDiv.contains(e.target)) { resultsDiv.style.display = 'none'; } });
    resultsDiv.addEventListener('click', e => e.stopPropagation());
});

// ========== CSRF PROTECTION ==========
$(document).ready(function () {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': window.appConfig.csrfToken } });
    $(document).ajaxError(function (event, xhr) { if (xhr.status === 419) { location.reload(); } });
    setInterval(function () {
        $.get('/refresh-csrf', function (data) {
            $('meta[name="csrf-token"]').attr('content', data.token);
            window.appConfig.csrfToken = data.token;
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': data.token } });
        });
    }, 110 * 60 * 1000);
});
// ========== CHECKOUT PAGE FUNCTIONS (FIXED - WITH AUTO APPLY) ==========
$(document).ready(function () {
    if (!window.location.pathname.includes('/checkout') && !$('#checkoutForm').length) {
        return;
    }

    console.log('Checkout initialized - WITH AUTO APPLY COUPON');

    var currencySymbol = window.appConfig.currencySymbol;
    var csrfToken = window.appConfig.csrfToken;
    var currentDiscount = 0;
    var appliedCouponCode = '';
    var shippingFee = 0;

    // ========== LOAD AND AUTO-APPLY COUPON FROM SESSION ==========
    function loadAndApplyCouponFromSession() {
        $.ajax({
            url: window.appConfig.routes.cartCouponStatus,
            method: 'GET',
            success: function(response) {
                console.log('Coupon from session on checkout:', response);
                if (response.success && response.applied) {
                    currentDiscount = parseFloat(response.discount_amount) || 0;
                    appliedCouponCode = response.coupon_code;
                    
                    // Update UI
                    $('#checkoutDiscountRow').show();
                    $('#checkoutDiscountAmount').text('-' + currencySymbol + ' ' + currentDiscount.toFixed(2));
                    $('#applyCheckoutCoupon').hide();
                    $('#removeCheckoutCoupon').show();
                    $('#checkoutCouponCode').val(appliedCouponCode).prop('disabled', true);
                    $('#checkoutCouponMessage').html('<div class="alert alert-success py-2 px-3">Coupon ' + appliedCouponCode + ' applied from cart!</div>');
                    
                    // Update total after coupon applied
                    updateTotalWithCoupon();
                    
                    // Auto-hide message after 3 seconds
                    setTimeout(function() {
                        $('#checkoutCouponMessage .alert').fadeOut('slow', function() { $(this).remove(); });
                    }, 3000);
                } else {
                    resetCouponUI();
                }
            },
            error: function() {
                resetCouponUI();
            }
        });
    }

    function resetCouponUI() {
        currentDiscount = 0;
        appliedCouponCode = '';
        $('#checkoutDiscountRow').hide();
        $('#applyCheckoutCoupon').show();
        $('#removeCheckoutCoupon').hide();
        $('#checkoutCouponCode').val('').prop('disabled', false);
    }

    function updateTotalWithCoupon() {
        var subtotal = parseFloat($('#checkoutSubtotal').text().replace(/[^0-9.-]/g, '')) || 0;
        var shipping = shippingFee;
        var total = subtotal + shipping - currentDiscount;
        $('#checkoutTotal').text(currencySymbol + ' ' + total.toFixed(2));
    }

    function loadCart() {
        $.ajax({
            url: window.appConfig.routes.checkoutCartData,
            method: 'GET',
            success: function(res) {
                console.log('Checkout cart data:', res);
                if (res.success) {
                    displayCartItems(res.items, res.total);
                    if (res.total > 0) calculateShipping(res.total);
                } else {
                    $('#checkoutCartItems').html('<div class="alert alert-danger">Error loading cart</div>');
                }
            },
            error: function(xhr) {
                console.error('Checkout cart error:', xhr);
                $('#checkoutCartItems').html('<div class="alert alert-danger">Error loading cart. Please refresh.</div>');
            }
        });
    }

    function displayCartItems(items, total) {
        var html = '';
        if (!items || items.length === 0) {
            html = '<div class="text-center py-4">Your cart is empty</div>';
        } else {
            items.forEach(function(item) {
                var subtotal = item.price * item.quantity;
                var variant = item.variant_name ? '<small class="text-muted d-block">' + escapeHtml(item.variant_name) + '</small>' : '';
                html += '<div class="d-flex justify-content-between align-items-start gap-3 mb-4 pb-2 border-bottom">' +
                    '<div class="flex-grow-1">' +
                    '<div class="fw-medium text-dark">' + escapeHtml(item.product_name) + '</div>' +
                    variant +
                    '<div class="d-flex align-items-center gap-2 mt-2">' +
                    '<span class="text-muted">Qty:</span>' +
                    '<span class="fw-semibold bg-light px-3 py-1 rounded">' + item.quantity + '</span>' +
                    '</div>' +
                    '</div>' +
                    '<div class="text-end">' +
                    '<span class="fw-bold text-main-600 text-nowrap">' + currencySymbol + ' ' + subtotal.toFixed(2) + '</span>' +
                    '</div>' +
                    '</div>';
            });
        }
        $('#checkoutCartItems').html(html);
        $('#checkoutSubtotal').text(currencySymbol + ' ' + total.toFixed(2));
        updateTotal(total);
    }

  function calculateShipping(cartTotal) {
    $.ajax({
        url: window.appConfig.routes.cartShippingCalc,
        method: 'POST',
        data: { cart_total: cartTotal, _token: csrfToken },
        success: function(res) {
            if (res.success) {
                shippingFee = res.shipping_fee;
                var remainingAmount = res.remaining_amount; // ✅ YEH ADD KAR
                
                if (shippingFee === 0) {
                    $('#checkoutShippingAmount').text('Free');
                    $('#checkoutShippingMessage').html('<div class="text-success"><i class="ph ph-truck"></i> Free Shipping</div>');
                } else {
                    $('#checkoutShippingAmount').text(currencySymbol + ' ' + shippingFee.toFixed(2));
                    if (res.max_order_amount && remainingAmount > 0) {
                        $('#checkoutShippingMessage').html('<div class="text-info">Add ' + currencySymbol + ' ' + remainingAmount.toFixed(2) + ' more for free shipping!</div>');
                    } else {
                        $('#checkoutShippingMessage').html('');
                    }
                }
                updateTotal(cartTotal);
            }
        },
        error: function(xhr) {
            console.error('Shipping calc error:', xhr);
        }
    });
}

    function updateTotal(cartTotal) {
        var total = cartTotal + shippingFee - currentDiscount;
        $('#checkoutTotal').text(currencySymbol + ' ' + total.toFixed(2));
        
        if (currentDiscount > 0) {
            $('#checkoutDiscountRow').show();
            $('#checkoutDiscountAmount').text('-' + currencySymbol + ' ' + currentDiscount.toFixed(2));
        } else {
            $('#checkoutDiscountRow').hide();
        }
    }

    function refreshTotals() {
        $.ajax({
            url: window.appConfig.routes.cartTotalCalc,
            method: 'GET',
            success: function(res) {
                if (res.success) {
                    $('#checkoutSubtotal').text(currencySymbol + ' ' + res.cart_total.toFixed(2));
                    calculateShipping(res.cart_total);
                }
            }
        });
    }

    function setCountry() {
        var map = { 'PKR': 'Pakistan', 'USD': 'United States', 'INR': 'India', 'GBP': 'United Kingdom', 'EUR': 'Germany', 'AED': 'UAE' };
        var currency = window.appConfig.currencySymbol.replace('$', '').replace('PKR', 'PKR').trim();
        var country = map[currency] || 'United States';
        $('#countryDisplay').val(country);
        $('#countryHidden').val(country);
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    // ========== APPLY COUPON BUTTON ==========
    $('#applyCheckoutCoupon').off('click').on('click', function() {
        var code = $('#checkoutCouponCode').val().trim();
        if (!code) {
            $('#checkoutCouponMessage').html('<div class="alert alert-warning">Enter coupon code</div>');
            return;
        }
        
        $('#checkoutCouponMessage').html('<div class="alert alert-info py-2 px-3"><i class="ph ph-spinner ph-spin me-2"></i> Applying coupon...</div>');
        
        $.ajax({
            url: window.appConfig.routes.cartTotalCalc,
            method: 'GET',
            success: function(res) {
                if (res.success) {
                    $.ajax({
                        url: window.appConfig.routes.cartApplyCoupon,
                        method: 'POST',
                        data: { coupon_code: code, cart_total: res.cart_total, _token: csrfToken },
                        success: function(couponRes) {
                            if (couponRes.success) {
                                currentDiscount = couponRes.discount_amount;
                                appliedCouponCode = couponRes.coupon_code;
                                $('#checkoutCouponMessage').html('<div class="alert alert-success py-2 px-3">' + couponRes.message + '</div>');
                                $('#checkoutDiscountRow').show();
                                $('#checkoutDiscountAmount').text('-' + currencySymbol + ' ' + currentDiscount.toFixed(2));
                                $('#applyCheckoutCoupon').hide();
                                $('#removeCheckoutCoupon').show();
                                $('#checkoutCouponCode').prop('disabled', true);
                                refreshTotals();
                                setTimeout(function() { $('#checkoutCouponMessage .alert').fadeOut('slow', function() { $(this).remove(); }); }, 5000);
                            } else {
                                $('#checkoutCouponMessage').html('<div class="alert alert-danger">' + couponRes.message + '</div>');
                            }
                        }
                    });
                }
            }
        });
    });

    // ========== REMOVE COUPON BUTTON ==========
    $('#removeCheckoutCoupon').off('click').on('click', function() {
        $.ajax({
            url: window.appConfig.routes.cartRemoveCoupon,
            method: 'POST',
            data: { _token: csrfToken },
            success: function() {
                currentDiscount = 0;
                appliedCouponCode = '';
                $('#checkoutDiscountRow').hide();
                $('#applyCheckoutCoupon').show();
                $('#removeCheckoutCoupon').hide();
                $('#checkoutCouponCode').val('').prop('disabled', false);
                $('#checkoutCouponMessage').html('<div class="alert alert-info py-2 px-3">Coupon removed successfully</div>');
                refreshTotals();
                setTimeout(function() { $('#checkoutCouponMessage .alert').fadeOut('slow', function() { $(this).remove(); }); }, 3000);
            }
        });
    });

    $('#checkoutForm').submit(function() {
        $('#placeOrderBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Processing...');
        $('#orderLoading').show();
        return true;
    });

    // ========== INITIALIZE ==========
    loadCart();
    setCountry();
    loadAndApplyCouponFromSession();  
});

// ========== QUICK VIEW FIX ==========
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('quickViewModal');
    if (!modal) return;

    modal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const productId = button.getAttribute('data-id');
        if (!productId) {
            console.error('No product ID found');
            return;
        }

        const contentDiv = document.getElementById('quickViewContent');
        contentDiv.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" style="width: 3rem; height: 3rem;"></div><p class="mt-3 text-muted">Loading product details...</p></div>';

        fetch(window.appConfig.routes.quickView + productId, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                contentDiv.innerHTML = data.html;
                // Reinitialize variant selection after content loads
                setTimeout(function() {
                    const dataDiv = document.getElementById('qv-product-data');
                    if (dataDiv && dataDiv.dataset.variants) {
                        try { window.qvVariants = JSON.parse(dataDiv.dataset.variants); } catch(e) {}
                    }
                }, 100);
            } else {
                contentDiv.innerHTML = '<div class="alert alert-danger text-center">Error loading product</div>';
            }
        })
        .catch(error => {
            console.error('Quick view error:', error);
            contentDiv.innerHTML = '<div class="alert alert-danger text-center">Error loading product. Please try again.</div>';
        });
    });

    modal.addEventListener('hidden.bs.modal', function() {
        document.getElementById('quickViewContent').innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" style="width: 3rem; height: 3rem;"></div><p class="mt-3 text-muted">Loading product details...</p></div>';
    });
});

// ========== VIEW MORE BUTTON FIX ==========
document.addEventListener('DOMContentLoaded', function() {
    let currentPage = 1;
    let loading = false;
    let hasMore = true;
    
    var viewMoreButton = document.getElementById('view-more-trending');
    var container = document.getElementById('trending-products-container');
    var buttonContainer = document.getElementById('trending-button-container');
    var loadingIndicator = document.getElementById('trending-loading');
    
    if (viewMoreButton) {
        viewMoreButton.addEventListener('click', function() {
            if (loading) return;
            loadMoreTrendingProducts();
        });
    }
    
    function loadMoreTrendingProducts() {
        loading = true;
        currentPage++;
        
        if (loadingIndicator) loadingIndicator.style.display = 'block';
        if (viewMoreButton) {
            viewMoreButton.disabled = true;
            viewMoreButton.innerHTML = 'Loading... <i class="ph ph-arrow-right ms-2"></i>';
        }
        
        var url = window.appConfig.routes.trendingProducts + '?page=' + currentPage;
        
        fetch(url, {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.html && data.html.trim() !== '') {
                container.insertAdjacentHTML('beforeend', data.html);
                hasMore = data.has_more;
                
                if (!hasMore && viewMoreButton && buttonContainer) {
                    viewMoreButton.remove();
                    buttonContainer.innerHTML = '<a href="' + window.appConfig.routes.products + '" class="fw-medium text-main-600 py-14 px-24 bg-main-50 rounded-pill line-height-1 hover-bg-main-600 hover-text-white">Shop Now <i class="ph ph-arrow-right ms-2"></i></a>';
                }
                
                // Reinitialize any countdown timers
                if (typeof initializeAllCountdowns === 'function') {
                    initializeAllCountdowns();
                }
            } else {
                hasMore = false;
                if (viewMoreButton && buttonContainer) {
                    viewMoreButton.remove();
                    buttonContainer.innerHTML = '<a href="' + window.appConfig.routes.products + '" class="fw-medium text-main-600 py-14 px-24 bg-main-50 rounded-pill line-height-1 hover-bg-main-600 hover-text-white">Shop Now <i class="ph ph-arrow-right ms-2"></i></a>';
                }
            }
        })
        .catch(error => {
            console.error('Trending products error:', error);
            currentPage--;
        })
        .finally(function() {
            loading = false;
            if (loadingIndicator) loadingIndicator.style.display = 'none';
            if (viewMoreButton && hasMore) {
                viewMoreButton.disabled = false;
                viewMoreButton.innerHTML = 'View More <i class="ph ph-arrow-right ms-2"></i>';
            }
        });
    }
});

function toggleWhatsappMenu() {
        const menu = document.getElementById('whatsapp-menu');
        if(menu.classList.contains('d-none')) {
            menu.classList.remove('d-none');
        } else {
            menu.classList.add('d-none');
        }
    }
    
    // Close when clicking outside
    document.addEventListener('click', function(event) {
        const widget = document.getElementById('whatsapp-widget');
        const menu = document.getElementById('whatsapp-menu');
        if(widget && !widget.contains(event.target) && menu && !menu.classList.contains('d-none')) {
            menu.classList.add('d-none');
        }
    });

    // ========== VENDOR DETAILS PAGE - FILTERING & PRICE SLIDER ==========
document.addEventListener('DOMContentLoaded', function() {
    // Check if we're on vendor details page
    if (document.getElementById('products-container') && typeof window.vendorData !== 'undefined') {
        const vendorSlug = window.vendorData.slug;
        const productsContainer = document.getElementById('products-container');
        const paginationContainer = document.getElementById('pagination-container');
        const statsSpan = document.getElementById('result-stats');
        
        console.log('Vendor details page initialized for:', vendorSlug);

        // Load products function
        function loadVendorProducts(url) {
            console.log('Loading vendor products:', url);
            
            productsContainer.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-main-600"></div><p class="mt-2">Loading products...</p></div>';

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log('Products response:', data);
                
                if (data.success) {
                    productsContainer.innerHTML = data.html;
                    
                    if (paginationContainer) {
                        paginationContainer.innerHTML = data.pagination || '';
                    }
                    
                    if (statsSpan) {
                        statsSpan.textContent = `Showing ${data.from || 0}-${data.to || 0} of ${data.total} results`;
                    }
                    
                    // Update URL without reload
                    const newUrl = url.replace('&ajax=1', '').replace('?ajax=1', '');
                    window.history.pushState({}, '', newUrl);
                } else {
                    productsContainer.innerHTML = '<div class="text-center py-5 text-danger">Error loading products</div>';
                }
            })
            .catch(error => {
                console.error('Load products error:', error);
                productsContainer.innerHTML = '<div class="text-center py-5 text-danger">Error loading products</div>';
            });
        }

        // Build URL with current filters
        function buildVendorUrl() {
            const baseUrl = `/vendor/${vendorSlug}`;
            const params = new URLSearchParams();
            
            // Category filter
            const activeCategory = document.querySelector('.category-filter.text-main-600');
            if (activeCategory) {
                const categoryId = activeCategory.dataset.category;
                if (categoryId) params.set('category', categoryId);
            }
            
            // Search filter
            const searchInput = document.getElementById('search-input');
            if (searchInput && searchInput.value) {
                params.set('search', searchInput.value);
            }
            
            // Sort filter
            const sortSelect = document.getElementById('sort-select');
            if (sortSelect && sortSelect.value !== 'latest') {
                params.set('sort', sortSelect.value);
            }
            
            // Price filter
            const customAmount = document.getElementById('custom-amount');
            if (customAmount && customAmount.value) {
                const numbers = customAmount.value.replace(/[^0-9-]/g, '').split('-');
                if (numbers && numbers.length >= 2) {
                    if (!(numbers[0] == '0' && numbers[1] == '10000')) {
                        params.set('price', numbers[0] + '-' + numbers[1]);
                    }
                }
            }
            
            // Page parameter
            const pageMatch = window.location.search.match(/page=(\d+)/);
            if (pageMatch) {
                params.set('page', pageMatch[1]);
            }
            
            params.set('ajax', '1');
            
            const url = params.toString() ? `${baseUrl}?${params.toString()}` : baseUrl;
            console.log('Built URL:', url);
            return url;
        }

        // ========== CATEGORY FILTER ==========
        document.querySelectorAll('.category-filter').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Category clicked:', this.dataset.category);
                
                // Update active state
                document.querySelectorAll('.category-filter').forEach(l => {
                    l.classList.remove('text-main-600', 'fw-semibold');
                });
                this.classList.add('text-main-600', 'fw-semibold');
                
                // Reset page and load
                const url = new URL(window.location.href);
                url.searchParams.delete('page');
                url.searchParams.delete('price');
                window.history.pushState({}, '', url.toString());
                
                loadVendorProducts(buildVendorUrl());
            });
        });

        // ========== SORT FILTER ==========
        const sortSelect = document.getElementById('sort-select');
        if (sortSelect) {
            sortSelect.addEventListener('change', function() {
                console.log('Sort changed:', this.value);
                
                const url = new URL(window.location.href);
                url.searchParams.delete('page');
                url.searchParams.delete('price');
                window.history.pushState({}, '', url.toString());
                
                loadVendorProducts(buildVendorUrl());
            });
        }

        // ========== SEARCH FILTER ==========
        const searchForm = document.getElementById('search-form');
        if (searchForm) {
            searchForm.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('Search submitted');
                
                const url = new URL(window.location.href);
                url.searchParams.delete('page');
                url.searchParams.delete('price');
                window.history.pushState({}, '', url.toString());
                
                loadVendorProducts(buildVendorUrl());
            });
        }

        // ========== PRICE SLIDER ==========
        if (document.getElementById('custom-price-range') && typeof $ !== 'undefined') {
            const minPrice = 0;
            const maxPrice = 10000;
            const currencySymbol = window.appConfig?.currencySymbol || '$';
            
            let currentMin = minPrice;
            let currentMax = maxPrice;
            
            // Get current price from URL
            const urlParams = new URLSearchParams(window.location.search);
            const priceParam = urlParams.get('price');
            if (priceParam) {
                const prices = priceParam.split('-');
                if (prices.length === 2) {
                    currentMin = parseInt(prices[0]) || minPrice;
                    currentMax = parseInt(prices[1]) || maxPrice;
                }
            }
            
            // Initialize slider
            if ($("#custom-price-range").slider("instance")) {
                $("#custom-price-range").slider("destroy");
            }
            
            $("#custom-price-range").slider({
                range: true,
                min: minPrice,
                max: maxPrice,
                values: [currentMin, currentMax],
                slide: function(event, ui) {
                    $("#custom-amount").val(currencySymbol + ' ' + ui.values[0] + ' - ' + currencySymbol + ' ' + ui.values[1]);
                },
                create: function() {
                    $("#custom-amount").val(currencySymbol + ' ' + currentMin + ' - ' + currencySymbol + ' ' + currentMax);
                }
            });
            
            // Price filter button
            const customFilterBtn = document.getElementById('custom-price-filter-btn');
            if (customFilterBtn) {
                // Remove old listeners by cloning
                const newFilterBtn = customFilterBtn.cloneNode(true);
                customFilterBtn.parentNode.replaceChild(newFilterBtn, customFilterBtn);
                
                newFilterBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Price filter clicked');
                    
                    const url = new URL(window.location.href);
                    url.searchParams.delete('page');
                    
                    const customAmount = document.getElementById('custom-amount');
                    if (customAmount && customAmount.value) {
                        const numbers = customAmount.value.replace(/[^0-9-]/g, '').split('-');
                        if (numbers && numbers.length >= 2) {
                            if (!(numbers[0] == '0' && numbers[1] == '10000')) {
                                url.searchParams.set('price', numbers[0] + '-' + numbers[1]);
                            } else {
                                url.searchParams.delete('price');
                            }
                        }
                    }
                    
                    window.history.pushState({}, '', url.toString());
                    loadVendorProducts(buildVendorUrl());
                });
            }
            
            // Hide old filter button if exists
            const oldFilterBtn = document.getElementById('price-filter-btn');
            if (oldFilterBtn) {
                oldFilterBtn.style.display = 'none';
            }
        }

        // ========== PAGINATION ==========
        document.addEventListener('click', function(e) {
            const paginationLink = e.target.closest('.pagination a');
            if (paginationLink) {
                e.preventDefault();
                console.log('Pagination clicked');
                
                const href = paginationLink.getAttribute('href');
                const pageMatch = href.match(/page=(\d+)/);
                
                if (pageMatch) {
                    const url = new URL(window.location.href);
                    url.searchParams.set('page', pageMatch[1]);
                    url.searchParams.set('ajax', '1');
                    
                    console.log('Pagination URL:', url.toString());
                    loadVendorProducts(url.toString());
                }
            }
        });
        
        // Initial load if needed (if URL has filters)
        const currentUrl = window.location.href;
        if (currentUrl.includes('?') && !currentUrl.includes('ajax=1')) {
            // Load with current filters
            setTimeout(() => {
                loadVendorProducts(buildVendorUrl());
            }, 100);
        }
    }
});

// ========== VENDOR LISTING PAGE - SEARCH & SORT ==========
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('vendors-container')) {
        const vendorsContainer = document.getElementById('vendors-container');
        const paginationContainer = document.getElementById('pagination-container');
        const statsSpan = document.getElementById('result-stats');
        const searchForm = document.getElementById('search-form');
        const sortSelect = document.getElementById('sort-select');

        console.log('Vendor listing page initialized');

        function loadVendors(url) {
            console.log('Loading vendors:', url);
            
            vendorsContainer.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-main-600"></div><p class="mt-2">Loading vendors...</p></div>';

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log('Vendors response:', data);
                
                if (data.success) {
                    vendorsContainer.innerHTML = data.html;
                    
                    if (paginationContainer) {
                        paginationContainer.innerHTML = data.pagination || '';
                    }
                    
                    if (statsSpan) {
                        statsSpan.textContent = `Showing ${data.from || 0}-${data.to || 0} of ${data.total} results`;
                    }
                    
                    // Update URL without reload
                    const newUrl = url.replace('&ajax=1', '').replace('?ajax=1', '');
                    window.history.pushState({}, '', newUrl);
                } else {
                    vendorsContainer.innerHTML = '<div class="text-center py-5 text-danger">Error loading vendors</div>';
                }
            })
            .catch(error => {
                console.error('Load vendors error:', error);
                vendorsContainer.innerHTML = '<div class="text-center py-5 text-danger">Error loading vendors</div>';
            });
        }

        function buildVendorsUrl() {
            const baseUrl = window.appConfig?.routes?.vendorIndex || '/vendors';
            const params = new URLSearchParams();
            
            // Search filter
            const searchInput = document.getElementById('search-input');
            if (searchInput && searchInput.value) {
                params.set('search', searchInput.value);
            }
            
            // Sort filter
            const sortSelect = document.getElementById('sort-select');
            if (sortSelect && sortSelect.value !== 'latest') {
                params.set('sort', sortSelect.value);
            }
            
            // Page parameter
            const pageMatch = window.location.search.match(/page=(\d+)/);
            if (pageMatch) {
                params.set('page', pageMatch[1]);
            }
            
            params.set('ajax', '1');
            
            const url = params.toString() ? `${baseUrl}?${params.toString()}` : baseUrl;
            console.log('Built vendors URL:', url);
            return url;
        }

        // Search form submit
        if (searchForm) {
            searchForm.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('Vendor search submitted');
                
                // Reset page
                const url = new URL(window.location.href);
                url.searchParams.delete('page');
                window.history.pushState({}, '', url.toString());
                
                loadVendors(buildVendorsUrl());
            });
        }

        // Sort change
        if (sortSelect) {
            sortSelect.addEventListener('change', function() {
                console.log('Vendor sort changed:', this.value);
                
                const url = new URL(window.location.href);
                url.searchParams.delete('page');
                window.history.pushState({}, '', url.toString());
                
                loadVendors(buildVendorsUrl());
            });
        }

        // Pagination clicks
        document.addEventListener('click', function(e) {
            const paginationLink = e.target.closest('.pagination a');
            if (paginationLink) {
                e.preventDefault();
                console.log('Vendor pagination clicked');
                
                const href = paginationLink.getAttribute('href');
                const pageMatch = href.match(/page=(\d+)/);
                
                if (pageMatch) {
                    const url = new URL(window.location.href);
                    url.searchParams.set('page', pageMatch[1]);
                    window.history.pushState({}, '', url.toString());
                    
                    loadVendors(buildVendorsUrl());
                }
            }
        });
        
        // Initial load if URL has filters
        const currentUrl = window.location.href;
        if (currentUrl.includes('?') && !currentUrl.includes('ajax=1')) {
            setTimeout(() => {
                loadVendors(buildVendorsUrl());
            }, 100);
        }
    }
});

// ========== QUICK VIEW MODAL - VARIANT SELECTION & ADD TO CART ==========
$(document).ready(function() {
    
    // ========== QUANTITY BUTTONS ==========
    $(document).on('click', '#modalQtyDown', function(e) {
        e.preventDefault();
        const qty = $('#modalQuantityInput');
        let val = parseInt(qty.val()) || 1;
        if (val > 1) {
            qty.val(val - 1);
        }
    });
    
    $(document).on('click', '#modalQtyUp', function(e) {
        e.preventDefault();
        const qty = $('#modalQuantityInput');
        let val = parseInt(qty.val()) || 1;
        let max = parseInt(qty.attr('max')) || 999;
        if (val < max) {
            qty.val(val + 1);
        }
    });
    
    $(document).on('change', '#modalQuantityInput', function(e) {
        let val = parseInt($(this).val()) || 1;
        let max = parseInt($(this).attr('max')) || 999;
        if (val < 1) $(this).val(1);
        if (val > max) {
            $(this).val(max);
            alert('Only ' + max + ' items available');
        }
    });
    
    // ========== VARIANT SELECTION - COLOR ==========
    $(document).on('click', '.qv-color-btn', function() {
        if ($(this).is('[disabled]')) return;
        
        const attribute = 'color';
        const value = $(this).data('value');
        
        // Remove active class from same attribute group
        $(`.qv-color-btn`).removeClass('active');
        $(this).addClass('active');
        
        // Store selection
        window.qvSelectedAttributes = window.qvSelectedAttributes || {};
        window.qvSelectedAttributes[attribute] = value;
        
        // Find matching variant
        findMatchingQVVariant();
    });
    
    // ========== VARIANT SELECTION - SIZE ==========
    $(document).on('click', '.qv-size-btn', function() {
        if ($(this).is('[disabled]')) return;
        
        const attribute = 'size';
        const value = $(this).data('value');
        
        // Remove active class from same attribute group
        $(`.qv-size-btn`).removeClass('active');
        $(this).addClass('active');
        
        // Store selection
        window.qvSelectedAttributes = window.qvSelectedAttributes || {};
        window.qvSelectedAttributes[attribute] = value;
        
        // Find matching variant
        findMatchingQVVariant();
    });
    
    // ========== VARIANT SELECTION - OTHER ATTRIBUTES ==========
    $(document).on('click', '.qv-attr-btn', function() {
        if ($(this).is('[disabled]')) return;
        
        const attribute = $(this).data('attribute');
        const value = $(this).data('value');
        
        // Remove active class from same attribute group
        $(`.qv-attr-btn[data-attribute="${attribute}"]`).removeClass('active');
        $(this).addClass('active');
        
        // Store selection
        window.qvSelectedAttributes = window.qvSelectedAttributes || {};
        window.qvSelectedAttributes[attribute] = value;
        
        // Find matching variant
        findMatchingQVVariant();
    });
    
    // ========== FIND MATCHING VARIANT ==========
    function findMatchingQVVariant() {
        const selectedAttrs = window.qvSelectedAttributes || {};
        const variantDataDiv = document.getElementById('qv-product-data');
        
        if (!variantDataDiv) {
            console.log('No variant data found');
            return;
        }
        
        let variantData = {};
        try {
            variantData = JSON.parse(variantDataDiv.dataset.variants || '{}');
        } catch(e) {
            console.error('Error parsing variant data:', e);
            return;
        }
        
        if (Object.keys(selectedAttrs).length === 0) {
            $('#modalSelectedVariantId').val('');
            $('#modalNoVariantMessage').html('<i class="ph ph-info"></i> Please select color/size');
            return;
        }
        
        let matchedVariantId = null;
        
        for (let vid in variantData) {
            const variant = variantData[vid];
            let match = true;
            
            for (let attr in selectedAttrs) {
                if (!variant.attributes || variant.attributes[attr] !== selectedAttrs[attr]) {
                    match = false;
                    break;
                }
            }
            
            if (match) {
                matchedVariantId = vid;
                break;
            }
        }
        
        if (matchedVariantId) {
            $('#modalSelectedVariantId').val(matchedVariantId);
            $('#modalNoVariantMessage').html('');
            updateQVariantUI(matchedVariantId, variantData);
        } else {
            $('#modalSelectedVariantId').val('');
            $('#modalNoVariantMessage').html('<span class="text-danger"><i class="ph ph-warning"></i> This combination is not available</span>');
        }
    }
    
    // ========== UPDATE UI WITH SELECTED VARIANT ==========
    function updateQVariantUI(variantId, variantData) {
        const variant = variantData[variantId];
        if (!variant) return;
        
        const currency = window.appConfig?.currencySymbol || '$';
        
        // Update price
        let price = parseFloat(variant.price) || 0;
        let salePrice = variant.sale_price ? parseFloat(variant.sale_price) : null;
        
        const priceEl = $('#modalCurrentPrice');
        const oldPriceEl = $('#modalOriginalPrice');
        const discountBadge = $('#modalDiscountBadge');
        const saleBadge = $('#modalSaleBadge');
        
        if (salePrice && salePrice > 0 && salePrice < price) {
            priceEl.text(currency + ' ' + salePrice.toFixed(2));
            if (oldPriceEl.length) {
                oldPriceEl.text(currency + ' ' + price.toFixed(2));
                oldPriceEl.parent().show();
            }
            if (discountBadge.length) {
                const discountPercent = Math.round(((price - salePrice) / price) * 100);
                discountBadge.text(discountPercent + '% OFF').show();
            }
            if (saleBadge.length) saleBadge.show();
        } else {
            priceEl.text(currency + ' ' + price.toFixed(2));
            if (oldPriceEl.length) oldPriceEl.parent().hide();
            if (discountBadge.length) discountBadge.hide();
            if (saleBadge.length) saleBadge.hide();
        }
        
        // Update stock
        const stockStatus = $('#modalStockText');
        const stockQuantity = $('#modalStockQuantity');
        
        if (variant.stock > 0) {
            stockStatus.text('In Stock').removeClass('text-danger').addClass('text-success');
            stockQuantity.text('(' + variant.stock + ' available)');
            $('#modalAddToCartBtn').prop('disabled', false).removeClass('opacity-50');
        } else {
            stockStatus.text('Out of Stock').removeClass('text-success').addClass('text-danger');
            stockQuantity.text('(0 available)');
            $('#modalAddToCartBtn').prop('disabled', true).addClass('opacity-50');
        }
        
        // Update SKU
        $('#modalProductSku').text(variant.sku || 'N/A');
        
        // Update max quantity
        $('#modalQuantityInput').attr('max', variant.stock > 0 ? variant.stock : 0);
        if (variant.stock === 0) {
            $('#modalQuantityInput').val(0);
        } else if (parseInt($('#modalQuantityInput').val()) > variant.stock) {
            $('#modalQuantityInput').val(1);
        }
        
        // Update images
        if (variant.images && variant.images.length > 0) {
            const mainImage = $('#modalMainProductImage');
            mainImage.attr('src', variant.images[0]);
            
            // Update thumbnails
            const thumbContainer = $('#modalThumbnailContainer');
            if (thumbContainer.length && variant.images.length > 1) {
                thumbContainer.empty();
                variant.images.forEach(img => {
                    const thumbDiv = $('<div>')
                        .addClass('qv-thumb-item')
                        .attr('data-image', img)
                        .html(`<img src="${img}" alt="Thumbnail">`)
                        .on('click', function() {
                            mainImage.attr('src', $(this).data('image'));
                        });
                    thumbContainer.append(thumbDiv);
                });
            }
        }
    }
    
    // ========== QUICK VIEW ADD TO CART ==========
    $(document).on('click', '#modalAddToCartBtn', function(e) {
        e.preventDefault();
        
        const btn = $(this);
        const productId = btn.data('product-id');
        const variantId = $('#modalSelectedVariantId').val();
        const quantity = $('#modalQuantityInput').val() || 1;
        
        // Check if variant selection is needed but not selected
        const hasVariants = $('.qv-color-btn, .qv-size-btn, .qv-attr-btn').length > 0;
        if (hasVariants && (!variantId || variantId === '')) {
            alert('Please select a variant first');
            return;
        }
        
        // Disable button and show loading
        const originalHtml = btn.html();
        btn.html('<span class="spinner-border spinner-border-sm me-2"></span> Adding...');
        btn.prop('disabled', true);
        
        $.ajax({
            url: window.appConfig.routes.cartAdd,
            method: 'POST',
            data: {
                product_id: productId,
                variant_id: variantId || null,
                quantity: quantity,
                _token: window.appConfig.csrfToken
            },
            success: function(response) {
                if (response.success) {
                    if (window.cartSystem) {
                        window.cartSystem.updateNavbarCartCount(response.count);
                        window.cartSystem.updateAllCartSections(response.items, response.total);
                    }
                    
                    btn.html('<i class="ph ph-check"></i> Added!');
                    
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('quickViewModal'));
                    if (modal) modal.hide();
                    
                    // Show offcanvas cart
                    try {
                        var offcanvas = new bootstrap.Offcanvas('#shoppingCart');
                        offcanvas.show();
                    } catch(e) {}
                    
                    setTimeout(() => {
                        btn.html(originalHtml);
                    }, 1500);
                } else {
                    btn.html(originalHtml);
                    alert(response.message || 'Failed to add to cart');
                }
            },
            error: function(xhr) {
                btn.html(originalHtml);
                let errorMsg = 'Error adding to cart';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                alert(errorMsg);
            },
            complete: function() {
                setTimeout(() => {
                    btn.prop('disabled', false);
                }, 500);
            }
        });
    });
    
    // ========== THUMBNAIL CLICK IN QUICK VIEW ==========
    $(document).on('click', '.qv-thumb-item', function() {
        const imageUrl = $(this).data('image');
        if (imageUrl) {
            $('#modalMainProductImage').attr('src', imageUrl);
        }
    });
});
