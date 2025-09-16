/**
 * QR Code generation functionality
 */
class QRCodeGenerator {
    constructor() {
        this.qrCode = null;
        this.init();
    }

    init() {
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setupQRCode());
        } else {
            this.setupQRCode();
        }
    }

    setupQRCode() {
        const qrContainer = document.getElementById('qrcode');
        const qrData = qrContainer ? qrContainer.getAttribute('data-url') : null;
        
        // Also check hidden input for URL
        const qrUrlInput = document.getElementById('qr-url');
        const finalQrData = qrData || (qrUrlInput ? qrUrlInput.value : null);
        
        if (qrContainer && finalQrData && typeof QRCode !== 'undefined') {
            this.generateQRCode(qrContainer, finalQrData);
        } else if (qrContainer && finalQrData) {
            // Load QRCode library if not available
            this.loadQRCodeLibrary().then(() => {
                this.generateQRCode(qrContainer, finalQrData);
            });
        }
    }

    loadQRCodeLibrary() {
        return new Promise((resolve, reject) => {
            if (typeof QRCode !== 'undefined') {
                resolve();
                return;
            }

            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/qrcode/1.5.3/qrcode.min.js';
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    generateQRCode(container, data, options = {}) {
        const defaultOptions = {
            text: data,
            width: 256,
            height: 256,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H,
            ...options
        };

        try {
            this.qrCode = new QRCode(container, defaultOptions);
            
            // Add accessibility attributes
            const qrImage = container.querySelector('img');
            if (qrImage) {
                qrImage.alt = `QR Code for: ${data}`;
                qrImage.setAttribute('role', 'img');
                qrImage.setAttribute('aria-label', `QR Code that links to ${data}`);
            }

            // Trigger custom event
            const qrGeneratedEvent = new CustomEvent('qrCodeGenerated', {
                detail: {
                    container: container,
                    data: data,
                    options: defaultOptions
                }
            });
            document.dispatchEvent(qrGeneratedEvent);

        } catch (error) {
            console.error('Error generating QR code:', error);
            this.showError(container, 'Error al generar el código QR');
        }
    }

    showError(container, message) {
        container.innerHTML = `
            <div class="qr-error">
                <p class="text-red-600 text-center">
                    <i class="fas fa-exclamation-triangle"></i>
                    ${message}
                </p>
            </div>
        `;
    }

    updateQRCode(newData, options = {}) {
        if (this.qrCode) {
            try {
                this.qrCode.clear();
                this.qrCode.makeCode(newData);
            } catch (error) {
                console.error('Error updating QR code:', error);
            }
        }
    }

    downloadQRCode(filename = null) {
        const qrImage = document.querySelector('#qrcode img');
        if (qrImage) {
            // Get filename from hidden input if not provided
            if (!filename) {
                const filenameInput = document.getElementById('qr-filename');
                filename = filenameInput ? filenameInput.value : 'qrcode.png';
            }
            
            const link = document.createElement('a');
            link.download = filename;
            link.href = qrImage.src;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    }

    printQRCode() {
        // Add print styles temporarily if not already present
        if (!document.getElementById('qr-print-styles')) {
            const printStyles = document.createElement('style');
            printStyles.id = 'qr-print-styles';
            printStyles.textContent = `
                @media print {
                    body * { visibility: hidden; }
                    #qrcode, #qrcode * { visibility: visible; }
                    #qrcode { position: absolute; top: 0; left: 0; }
                }
            `;
            document.head.appendChild(printStyles);
        }

        window.print();
    }

    // Static methods for global access
    static generate(container, data, options = {}) {
        const generator = new QRCodeGenerator();
        generator.generateQRCode(container, data, options);
        return generator;
    }

    static download(filename) {
        if (window.qrCodeGenerator) {
            window.qrCodeGenerator.downloadQRCode(filename);
        }
    }

    static print() {
        if (window.qrCodeGenerator) {
            window.qrCodeGenerator.printQRCode();
        }
    }
}

// Initialize QR code generator
window.qrCodeGenerator = new QRCodeGenerator();

// Global functions for backward compatibility
window.generateQRCode = function(container, data, options) {
    return QRCodeGenerator.generate(container, data, options);
};

window.downloadQRCode = function(filename) {
    QRCodeGenerator.download(filename);
};

window.printQRCode = function() {
    QRCodeGenerator.print();
};

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = QRCodeGenerator;
}