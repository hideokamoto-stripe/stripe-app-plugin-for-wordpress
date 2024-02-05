
document.addEventListener( 'DOMContentLoaded', () => {
    const button = document.getElementById( 'fetch-customer-button' );
    const rowElement = document.getElementById( 'stripe-sample-customer-table' );
    const targetAttributes = [{
        label: 'Customer ID',
        targetAttributeName: 'id'
    }, {
        label: 'Name',
        targetAttributeName: 'name'
    }, {
        label: 'Email',
        targetAttributeName: 'email'
    }];
    button.addEventListener( 'click', async () => {
        const response = await fetch( '/wp-json/stripe-apps/v1/customers' );
        const customers = await response.json()
        if ( response.status > 399 ) {
            const errorResponseElement = document.createElement( 'p' );
            errorResponseElement.textContent = `${customers.code}: ${customers.message}`;
            rowElement.replaceWith( errorResponseElement );
            return;
        }
        if ( ! customers || customers.length < 1 ) {
            const noCustomerElement = document.createElement( 'p' );
            noCustomerElement.textContent = 'No customer registered. Please add customer on Stripe Dashboard.';
            rowElement.replaceWith( noCustomerElement );
        }
        const tableElement = document.createElement( 'table' );
        tableElement.setAttribute( 'class', 'wp-list-table widefat fixed striped table-view-list' );
        
        const tableHeadElement = document.createElement( 'thead' );
        const tableHeadRowElement = document.createElement( 'tr' );
        const tableHadIDElement = document.createElement( 'th' );
        tableHadIDElement.textContent = 'No.';
        tableHeadRowElement.appendChild( tableHadIDElement );
        targetAttributes.forEach(target => {
            const headElement = document.createElement( 'th' );
            headElement.textContent = target.label;
            tableHeadRowElement.appendChild( headElement );
        })
        tableHeadElement.appendChild( tableHeadRowElement );

        const tableBodyElement = document.createElement( 'tbody' );
        customers.forEach( ( customer, i ) => {
            const customerLineElement = document.createElement( 'tr' );
            const lineIdElement = document.createElement( 'td' );
            lineIdElement.textContent = i;
            customerLineElement.appendChild( lineIdElement );

            targetAttributes.forEach(target => {
                const attributeElement = document.createElement( 'td' );
                attributeElement.textContent = customer[ target.targetAttributeName ];
                customerLineElement.appendChild( attributeElement );
            });
            tableBodyElement.appendChild( customerLineElement );
        });
        tableElement.appendChild( tableHeadElement );
        tableElement.appendChild( tableBodyElement );
        rowElement.appendChild( tableElement );
    } );
})