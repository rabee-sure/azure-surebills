import faker from 'faker'

context('Register', () => {
    const email = faker.internet.email();
    const password = faker.internet.password()+'dddAA';

    // beforeEach(() => {
    //     cy.refreshDatabase().seed();
    // });
    before(() => {
        cy.refreshDatabase().seed();
        // cy.create('App\\User', {
        //     'business_name_en': 'Ghanem',
        //     'name': 'Abdullah Ghanem',
        //     'email': '3bdullah.ghanem@gmail.com',
        //     'mobile': '50002002',
        //     'password': '123456789',
        // })
    });

    it('Open Registeration Form', () => {
        cy.visit('/');
        cy.get('.register').click();
        // cy.get('.changeLang a').click();
        // cy.contains('Register a new account');
        cy.assertRedirect('register');
    });

    it('Register to the system', () => {
        cy.visit('/register');
        cy.get('#business_name_en').type('test');
        cy.get('#name').type('test');
        cy.get('#email').type(email);
        cy.get('#mobile').type('503333333');
        cy.get('#password').type(password);
        cy.get('#password-confirm').type(password);
        cy.get('.custom-checkbox label').click();
        cy.get('.login_button').click();
       
        // cy.should('contains', 'اكد على رقم هاتفك او جوالك');
        cy.assertRedirect('mobile_verify');
    });


    // it('resend the verfication code', () => {
    //     // cy.visit('/login');
    //     // cy.get('#email').type(email);
    //     // cy.get('#password').type(password);
    //     // cy.get('.login_button').click();
    //     cy.reload();
    //     cy.wait(60000);
    //     cy.reload();

    //     cy.get('.didnt_get_pin a').click();
        
    //     cy.should('be.contains', 'سيتم إعادة إرسال الرقم السري');
    // });

    it('verify the mobile phone', () => {
        cy.visit('/login');
        cy.get('#email').type(email);
        cy.get('#password').type(password);
        cy.get('.login_button').click();

        cy.get('.verify_phone_page input').type('0000');
        cy.get('.verify_phone_page button').click();
        cy.assertRedirect('account');
    });



    it('complete privite information', () => {
        cy.visit('/login');
        cy.get('#email').type(email);
        cy.get('#password').type(password);
        cy.get('.login_button').click();

        cy.get('select').select('1');

        cy.get('#next').click();
        // cy.should('be.contains', 'نوع الترخيص');
    });

    it('View and close license Type pop up', () => {
        cy.visit('/login');
        cy.get('#email').type(email);
        cy.get('#password').type(password);
        cy.get('.login_button').click();

        cy.get('#next').click();
        cy.get('.license_button').click();
        cy.get('.license_type_modal').should('have.class', 'show');

        cy.get('.license_type_modal').find('button').find('span').contains("×").click({force: true})
        cy.get('body').focus()

        // cy.get('.license_type_modal').should('not.have.class', 'show');
    });

    it('View license Type', () => {
        cy.visit('/login');
        cy.get('#email').type(email);
        cy.get('#password').type(password);
        cy.get('.login_button').click();

        cy.get('#next').click();
        cy.get('#license_type').select('Freelance').should('have.value', 'Freelance');
        cy.get('#license_type').select('Commercial Record').should('have.value', 'Commercial Record');
    });


    // it('complete Business Information', () => {
    //     cy.visit('/login');
    //     cy.get('#email').type(email);
    //     cy.get('#password').type(password);
    //     cy.get('.login_button').click();

    //     cy.get('#next').click();
    //     cy.get('#license_type').select('Freelance').should('have.value', 'Freelance');
    //     cy.get('#business_name_ar').type('business Name Ar');
    //     cy.get('#business_mobile').type('300309338');
    //     cy.get('#business_address').type('Business Address');
    //     cy.get('#next').click();
    // });

});