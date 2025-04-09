/**
 * Print customer profile with all details in landscape orientation
 * @param {Object} customer - Customer object with all details
 */
export function printCustomerProfile(customer) {
    // Create a new window for printing
    const printWindow = window.open('', '_blank');

    // Generate complete HTML with all customer information
    const printContent = `
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Customer Profile - ${customer.name}</title>
        <style>
            @media print {
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.3;
                    color: #333;
                    margin: 0;
                    padding: 0;
                    font-size: 12px;
                }
                .print-container {
                    padding: 15px;
                    max-width: 100%;
                }
                .print-header {
                    text-align: center;
                    margin-bottom: 15px;
                    padding-bottom: 5px;
                    border-bottom: 1px solid #4f46e5;
                }
                .print-title {
                    font-size: 18px;
                    font-weight: bold;
                    color: #4f46e5;
                    margin-bottom: 2px;
                    margin-top: 0;
                }
                .print-subtitle {
                    font-size: 14px;
                    color: #6b7280;
                    margin-top: 0;
                    margin-bottom: 2px;
                }
                .print-date {
                    font-size: 12px;
                    margin-top: 0;
                    margin-bottom: 2px;
                }
                .print-layout {
                    display: flex;
                    gap: 15px;
                }
                .print-layout-left {
                    flex: 2;
                }
                .print-layout-right {
                    flex: 1;
                }
                .print-section {
                    margin-bottom: 15px;
                }
                .print-section-title {
                    font-size: 14px;
                    font-weight: bold;
                    color: #1f2937;
                    margin-bottom: 8px;
                    margin-top: 0;
                    padding-bottom: 2px;
                    border-bottom: 1px solid #e5e7eb;
                }
                .print-grid {
                    display: grid;
                    grid-template-columns: 1fr 1fr 1fr;
                    gap: 10px;
                }
                .print-detail-item {
                    margin-bottom: 8px;
                }
                .print-detail-label {
                    font-weight: bold;
                    color: #6b7280;
                    margin-bottom: 2px;
                    font-size: 11px;
                }
                .print-detail-value {
                    color: #111827;
                    font-size: 12px;
                }
                .print-images {
                    display: grid;
                    grid-template-columns: 1fr;
                    gap: 10px;
                    margin-bottom: 10px;
                }
                .print-image-container {
                    background-color: #f9fafb;
                    border: 1px solid #e5e7eb;
                    border-radius: 4px;
                    padding: 5px;
                    margin-bottom: 10px;
                }
                .print-image-title {
                    font-weight: bold;
                    margin-bottom: 5px;
                    font-size: 12px;
                }
                .print-image {
                    width: 100%;
                    max-height: 180px;
                    object-fit: contain;
                }
                .address-details-grid {
                    display: grid;
                    grid-template-columns: 1fr;
                    gap: 10px;
                }
                .address-details-item {
                    background-color: #f9fafb;
                    border: 1px solid #e5e7eb;
                    border-radius: 4px;
                    padding: 8px;
                    margin-bottom: 10px;
                }
                .address-details-title {
                    font-weight: bold;
                    font-size: 12px;
                    margin-bottom: 5px;
                    padding-bottom: 2px;
                    border-bottom: 1px solid #e5e7eb;
                }
                .address-details-content {
                    font-size: 12px;
                }
                .print-footer {
                    text-align: center;
                    margin-top: 15px;
                    padding-top: 5px;
                    border-top: 1px solid #e5e7eb;
                    font-size: 10px;
                    color: #6b7280;
                }
                .print-footer p {
                    margin: 2px 0;
                }
                @page {
                    size: A4 landscape;
                    margin: 1cm;
                }
            }
        </style>
    </head>
    <body>
        <div class="print-container">
            <div class="print-header">
                <h1 class="print-title">${customer.name || ''}</h1>
                <p class="print-subtitle">Customer Detailed Profile</p>
                <p class="print-date">Created: ${formatDate(customer.created_at)}</p>
            </div>

            <div class="print-layout">
                <div class="print-layout-left">
                    <!-- Customer Information in 3 columns -->
                    <div class="print-section">
                        <h2 class="print-section-title">Customer Information</h2>
                        <div class="print-grid">
                            <div class="print-detail-item">
                                <div class="print-detail-label">Full Name</div>
                                <div class="print-detail-value">${customer.name || 'N/A'}</div>
                            </div>
                            <div class="print-detail-item">
                                <div class="print-detail-label">Name (Bangla)</div>
                                <div class="print-detail-value">${customer.name_bn || 'N/A'}</div>
                            </div>
                            <div class="print-detail-item">
                                <div class="print-detail-label">NID Number</div>
                                <div class="print-detail-value">${customer.nid_number || 'N/A'}</div>
                            </div>
                            <div class="print-detail-item">
                                <div class="print-detail-label">Father's Name</div>
                                <div class="print-detail-value">${customer.father_name || 'N/A'}</div>
                            </div>
                            <div class="print-detail-item">
                                <div class="print-detail-label">Mother's Name</div>
                                <div class="print-detail-value">${customer.mother_name || 'N/A'}</div>
                            </div>
                            <div class="print-detail-item">
                                <div class="print-detail-label">Phone Number</div>
                                <div class="print-detail-value">${customer.phone_number || 'N/A'}</div>
                            </div>
                            <div class="print-detail-item">
                                <div class="print-detail-label">Spouse Name</div>
                                <div class="print-detail-value">${customer.spouse_name || 'N/A'}</div>
                            </div>
                            <div class="print-detail-item">
                                <div class="print-detail-label">Date of Birth</div>
                                <div class="print-detail-value">${formatDate(customer.dob) || 'N/A'}</div>
                            </div>
                            ${customer.rejected_by ? `
                            <div class="print-detail-item">
                                <div class="print-detail-label">Rejected By</div>
                                <div class="print-detail-value">${customer.rejected_by || 'N/A'}</div>
                            </div>
                            ` : `
                            <div class="print-detail-item">
                                <div class="print-detail-label">Branch</div>
                                <div class="print-detail-value">${customer.branch?.branch_name || 'N/A'}</div>
                            </div>`}
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="print-section">
                        <h2 class="print-section-title">Additional Information</h2>
                        <div class="address-details-grid">
                            <div class="address-details-item">
                                <div class="address-details-title">Address Information</div>
                                <div class="address-details-content">
                                    ${customer.address || 'No address provided'}
                                </div>
                            </div>
                            <div class="address-details-item">
                                <div class="address-details-title">Details</div>
                                <div class="address-details-content">
                                    ${customer.details || 'No additional details'}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="print-layout-right">
                    <!-- NID Images Section -->
                    ${customer.nid_part_1 || customer.nid_part_2 ? `
                    <div class="print-section">
                        <h2 class="print-section-title">NID Documents</h2>
                        <div class="print-images">
                            ${customer.nid_part_1 ? `
                            <div class="print-image-container">
                                <div class="print-image-title">NID Part 1</div>
                                <img src="${getImageUrl(customer.nid_part_1)}" alt="NID Part 1" class="print-image">
                            </div>
                            ` : ''}
                            ${customer.nid_part_2 ? `
                            <div class="print-image-container">
                                <div class="print-image-title">NID Part 2</div>
                                <img src="${getImageUrl(customer.nid_part_2)}" alt="NID Part 2" class="print-image">
                            </div>
                            ` : ''}
                        </div>
                    </div>
                    ` : ''}

                    <!-- Branch and Created By -->
                    <div class="print-section">
                        <h2 class="print-section-title">Organization Info</h2>
                        <div class="address-details-grid">
                            <div class="address-details-item">
                                <div class="print-detail-label">Branch</div>
                                <div class="print-detail-value">${customer.branch?.branch_name || 'N/A'}</div>
                            </div>
                            <div class="address-details-item">
                                <div class="print-detail-label">Created By</div>
                                <div class="print-detail-value">${customer.user?.name || 'N/A'}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="print-footer">
                <p>Printed on ${new Date().toLocaleDateString()} at ${new Date().toLocaleTimeString()}</p>
                <p>Copyright ${new Date().getFullYear()} Mousumi NGO. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    `;

    // Write the content to the print window
    printWindow.document.open();
    printWindow.document.write(printContent);
    printWindow.document.close();

    // Wait for images to load before printing
    printWindow.onload = function() {
        // Print and close the window after printing
        printWindow.print();
        printWindow.onafterprint = function() {
            printWindow.close();
        };
    };
}

// Make sure these functions are exported
export function formatDate(date) {
    if (!date) return '';
    const dateObj = new Date(date);
    const day = String(dateObj.getDate()).padStart(2, '0');
    const month = String(dateObj.getMonth() + 1).padStart(2, '0');
    const year = dateObj.getFullYear();
    return `${day}/${month}/${year}`;
}

export function getImageUrl(imagePath) {
    return imagePath ? `${location.origin}/storage/app/public/${imagePath}` : '';
}

// Export default object for alternative import style
export default {
    formatDate,
    getImageUrl,
    printCustomerProfile
};
