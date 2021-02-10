context('Login', () => {
    // beforeEach(() => {
    //     cy.refreshDatabse()
    // });
    before(() => {
        cy.refreshDatabase().seed();

        cy.create('App\\Models\\User')
    });
    context('Login With Invalid Credetials', () => {
        it.only('Open Login Form', () => {
            cy.visit('/');
            cy.get('.login').click();
            cy.assertRedirect('login');
        });

        it('Login To the System', () => {
            cy.visit('/login');
            cy.get('#email').type('3bdullah.ghanem@gmail.com');
            cy.get('#password').type('123456789');
            cy.get('.login_button').click();
            cy.get('.user').contains('Abdullah Ghanem');
        });

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