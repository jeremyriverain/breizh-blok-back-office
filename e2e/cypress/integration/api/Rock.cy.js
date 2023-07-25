/// <reference types="cypress" />

context('Rock', () => {
  before(() => {
      cy.task('loadDb')
  })

  it('lists rocks', () => {
      cy.request('/rocks').then(response => {
          expect(response.body['hydra:totalItems']).to.eq(3)
      }) 
    })

  it('gets a rock', () => {
      cy.request('/rocks/1').then(response => {
          expect(response.body['boulders'].length).to.eq(2)
      })
  })
    
  it('cannot delete a rock', () => {
      cy.request({url :'/rocks/1', method: 'DELETE', failOnStatusCode: false}).then(response => {
          expect(response.status).to.eq(405)
      })
  })
 
  it('cannot create a rock', () => {
      cy.request({url :'/rocks', method: 'POST', body: {}, failOnStatusCode: false}).then(response => {
          expect(response.status).to.eq(405)
      })
  })

  it('cannot edit a rock', () => {
      cy.request({url :'/rocks/1', method: 'PUT', body: {}, failOnStatusCode: false}).then(response => {
          expect(response.status).to.eq(405)
      })

      cy.request({url :'/rocks/1', method: 'PATCH', body: {}, failOnStatusCode: false}).then(response => {
          expect(response.status).to.eq(405)
      })
  })

})