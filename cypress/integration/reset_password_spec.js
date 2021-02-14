context('Reset Password', () => {
    before(() => {
        cy.refreshDatabase().seed();
        cy.create('App\\User', {
            'business_name_en': 'Ghanem',
            'name': 'Abdullah Ghanem',
            'email': '3bdullah.ghanem@gmail.com',
            'mobile': '50002002',
        })
    });


    it('Forget Password', () => {
        cy.visit('/login');
        cy.get('#forgot_password').click();
        cy.assertRedirect('password/reset');
    });

    it('Request Resetting Pasword ( Valid )', () => {
        cy.visit('/password/reset');
        cy.get('#email').type('3bdullah.ghanem@gmail.com');
        cy.get('.login_button').click();
        cy.contains('تم إرسال تفاصيل استعادة كلمة المرور الخاصة بك إلى بريدك الإلكتروني!');
        cy.get('.alert').should('have.class', 'alert-success');
    });

    it('Request Resetting Pasword ( InValid )', () => {
        cy.visit('/password/reset');
        cy.get('#email').type('ghanem@gmail.com');
        cy.get('.login_button').click();
        cy.contains('لم يتم العثور على أيّ حسابٍ بهذا العنوان الإلكتروني.');
        cy.get('.alert').should('have.class', 'alert-danger');
    });

});