context('Login', () => {
    // beforeEach(() => {
    //     cy.refreshDatabse()
    // });
    before(() => {
        cy.refreshDatabase().seed();

        cy.create('App\\User', {
            'business_name_en': 'Ghanem',
            'name': 'Abdullah Ghanem',
            'email': '3bdullah.ghanem@gmail.com',
            'mobile': '50002002',
            'password': '123456789',
        })
    });
    context('Login With Invalid Credetials', () => {
        it('required vaild email', () => {
            cy.visit('/');
            cy.get('.login').click();
            cy.get('#email').type('abdullahghanem');
            cy.get('#password').focus();
            cy.get('.login_button').click();
            cy.contains('كلمة المرور مطلوب.');
        });

        it('Forget Password', () => {
            cy.visit('/login');
            cy.get('#forgot_password').click();
            cy.assertRedirect('password/reset');
        });
    });


});