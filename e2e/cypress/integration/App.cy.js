/// <reference types="cypress" />

context('App', () => {
  it('Privacy policy is accessible', () => {
      cy.visit('/privacy-policy')
      cy.get('h1').contains('POLITIQUE DE CONFIDENTIALITÉ DE BREIZH BLOK')
  })

})
