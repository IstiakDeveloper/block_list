export function formatDate(dateString) {
    return dateString
        ? new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        })
        : 'N/A';
}

export function getImageUrl(imagePath) {
    return imagePath
        ? `${location.origin}/storage/app/public/${imagePath}`
        : '/default-image.png';
}

export function printCustomerProfile(customer) {
    const currentDate = new Date().toLocaleDateString('en-US', {
        year: 'numeric', month: 'long', day: 'numeric'
    });

    const printWindow = window.open('', '_blank', 'width=800,height=600');
    printWindow.document.write(`
        <html>
            <head>
                <title>Customer Profile</title>
                <style>
                    @page {
                        size: A4;
                        margin: 10mm;
                    }
                    body {
                        font-family: Arial, sans-serif;
                        font-size: 10pt;
                        line-height: 1.4;
                        color: #333;
                        max-width: 210mm;
                        margin: 0 auto;
                    }
                    .header {
                        display: flex;
                        justify-content: space-between;
                        border-bottom: 1px solid #0066cc;
                        padding-bottom: 10px;
                        margin-bottom: 15px;
                    }
                    .section {
                        margin-bottom: 15px;
                        border: 1px solid #e0e0e0;
                        padding: 10px;
                        border-radius: 3px;
                    }
                    .section-title {
                        color: #0066cc;
                        font-weight: bold;
                        border-bottom: 1px solid #e0e0e0;
                        padding-bottom: 5px;
                        margin-bottom: 8px;
                    }
                    .detail-row {
                        display: flex;
                        margin-bottom: 5px;
                    }
                    .detail-label {
                        font-weight: bold;
                        width: 120px;
                        color: #333;
                    }
                    .detail-value {
                        flex-grow: 1;
                        color: #666;
                    }
                    .nid-images {
                        display: flex;
                        justify-content: space-between;
                        margin-top: 10px;
                    }
                    .nid-image {
                        width: 48%;
                        border: 1px solid #e0e0e0;
                        padding: 5px;
                        text-align: center;
                    }
                    .nid-image img {
                        max-width: 100%;
                        max-height: 200px;
                        object-fit: contain;
                    }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1>Customer Profile</h1>
                    <p>Generated: ${currentDate}</p>
                </div>

                <div class="section">
                    <div class="section-title">Personal Information</div>
                    <div class="detail-row">
                        <div class="detail-label">Full Name:</div>
                        <div class="detail-value">${customer.name || 'N/A'}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Name (Bangla):</div>
                        <div class="detail-value">${customer.name_bn || 'N/A'}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Father's Name:</div>
                        <div class="detail-value">${customer.father_name || 'N/A'}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Mother's Name:</div>
                        <div class="detail-value">${customer.mother_name || 'N/A'}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Date of Birth:</div>
                        <div class="detail-value">${formatDate(customer.dob)}</div>
                    </div>
                </div>

                <div class="section">
                    <div class="section-title">Contact Details</div>
                    <div class="detail-row">
                        <div class="detail-label">NID Number:</div>
                        <div class="detail-value">${customer.nid_number || 'N/A'}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Phone Number:</div>
                        <div class="detail-value">${customer.phone_number || 'N/A'}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Address:</div>
                        <div class="detail-value">${customer.address || 'N/A'}</div>
                    </div>
                </div>

                <div class="section">
                    <div class="section-title">NID Documents</div>
                    <div class="nid-images">
                        <div class="nid-image">
                            <p>NID Part 1</p>
                            ${customer.nid_part_1
            ? `<img src="${getImageUrl(customer.nid_part_1)}" alt="NID Part 1">`
            : '<p>No Image</p>'}
                        </div>
                        <div class="nid-image">
                            <p>NID Part 2</p>
                            ${customer.nid_part_2
            ? `<img src="${getImageUrl(customer.nid_part_2)}" alt="NID Part 2">`
            : '<p>No Image</p>'}
                        </div>
                    </div>
                </div>
            </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}
export function truncateText(text, maxLength = 50) {
    return text && text.length > maxLength
        ? text.substring(0, maxLength) + '...'
        : text;
}

export function capitalizeFirstLetter(string) {
    return string
        ? string.charAt(0).toUpperCase() + string.slice(1)
        : '';
}
