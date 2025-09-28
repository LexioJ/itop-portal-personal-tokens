/**
 * Personal Tokens Portal JavaScript
 * Handles token management interactions in the Portal UI
 */

$(document).ready(function() {
    var currentTokenId = null;
    
    // Create token button click
    $('#create-token-btn').on('click', function() {
        $('#createTokenModal').modal('show');
    });
    
    // Create token form submit
    $('#createTokenForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = {
            application: $('#token-application').val(),
            scope: $('#token-scope').val(),
            expiration_days: $('#token-expiration').val()
        };
        
        $.ajax({
            url: GetAbsoluteUrlAppRoot() + 'pages/exec.php',
            type: 'POST',
            data: {
                exec_module: 'itop-portal-personal-tokens',
                exec_page: 'ajax.handler',
                operation: 'create_token',
                data: JSON.stringify(formData)
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#createTokenModal').modal('hide');
                    $('#generated-token').val(response.token);
                    $('#tokenCreatedModal').modal('show');
                    // Reload the page after closing the success modal
                    $('#tokenCreatedModal').on('hidden.bs.modal', function() {
                        location.reload();
                    });
                } else {
                    showAlert('danger', response.message || 'Failed to create token');
                }
            },
            error: function() {
                showAlert('danger', 'An error occurred while creating the token');
            }
        });
    });
    
    // Copy token to clipboard
    $('#copy-token-btn').on('click', function() {
        var tokenInput = document.getElementById('generated-token');
        tokenInput.select();
        tokenInput.setSelectionRange(0, 99999); // For mobile devices
        
        try {
            document.execCommand('copy');
            $('#copy-success').fadeIn().delay(2000).fadeOut();
        } catch (err) {
            showAlert('warning', 'Please copy the token manually');
        }
    });
    
    // Delete token button click
    $('.delete-token').on('click', function() {
        currentTokenId = $(this).data('id');
        $('#confirmDeleteModal').modal('show');
    });
    
    // Confirm delete
    $('#confirm-delete-btn').on('click', function() {
        if (currentTokenId) {
            $.ajax({
                url: GetAbsoluteUrlAppRoot() + 'pages/exec.php',
                type: 'POST',
                data: {
                    exec_module: 'itop-portal-personal-tokens',
                    exec_page: 'ajax.handler',
                    operation: 'delete_token',
                    token_id: currentTokenId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#confirmDeleteModal').modal('hide');
                        location.reload();
                    } else {
                        showAlert('danger', response.message || 'Failed to delete token');
                    }
                },
                error: function() {
                    showAlert('danger', 'An error occurred while deleting the token');
                }
            });
        }
    });
    
    // Regenerate token button click
    $('.regenerate-token').on('click', function() {
        currentTokenId = $(this).data('id');
        $('#confirmRegenerateModal').modal('show');
    });
    
    // Confirm regenerate
    $('#confirm-regenerate-btn').on('click', function() {
        if (currentTokenId) {
            $.ajax({
                url: GetAbsoluteUrlAppRoot() + 'pages/exec.php',
                type: 'POST',
                data: {
                    exec_module: 'itop-portal-personal-tokens',
                    exec_page: 'ajax.handler',
                    operation: 'regenerate_token',
                    token_id: currentTokenId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#confirmRegenerateModal').modal('hide');
                        $('#generated-token').val(response.token);
                        $('#tokenCreatedModal').modal('show');
                        // Reload the page after closing the success modal
                        $('#tokenCreatedModal').on('hidden.bs.modal', function() {
                            location.reload();
                        });
                    } else {
                        showAlert('danger', response.message || 'Failed to regenerate token');
                    }
                },
                error: function() {
                    showAlert('danger', 'An error occurred while regenerating the token');
                }
            });
        }
    });
    
    // Helper function to show alerts
    function showAlert(type, message) {
        var alertHtml = '<div class="alert alert-' + type + ' alert-dismissible" role="alert">' +
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                        '<span aria-hidden="true">&times;</span></button>' +
                        message + '</div>';
        $('.personal-tokens-container').prepend(alertHtml);
        
        // Auto-hide after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow', function() {
                $(this).remove();
            });
        }, 5000);
    }
});