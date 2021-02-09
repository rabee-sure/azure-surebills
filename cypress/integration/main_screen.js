context('Main Screen', () => {
    it('Check Main Screen Details', () => {
        cy.visit('/');
        cy.get('.main_menu').contains('شور بيلز');
        cy.get('.main_menu').contains('المميزات');
        cy.get('.main_menu').contains('اتصل بنا');
        cy.get('.main_menu').contains('تسجيل');
    });

    it('The Body contains', () => {
        cy.visit('/');
        cy.get('#main_slider').contains('سجل الآن');
        cy.get('#start_work').contains('سجل مجاناً');
        cy.get('#faq').contains('الأسئلة الشائعة');
        cy.get('#faq').contains('اتصل بنا');
    });

    it('The Footer contains', () => {
        cy.visit('/');
        cy.get('footer').contains('الخصوصية');
        cy.get('footer').contains('كيف نعمل');
        cy.get('footer').contains('اتصل بنا');
        cy.get('footer').contains('الشروط والاحكام');
        cy.get('footer').contains('الاسئلة الشائعة');
    });


    it('Open Contact Us', () => {
        cy.visit('/');
        cy.get('.contactus_now a').click();
        cy.assertRedirect('contact');

        cy.visit('/');
        cy.get(".footer_menu a[title^='اتصل بنا']").click();
        cy.assertRedirect('contact');
    });
});