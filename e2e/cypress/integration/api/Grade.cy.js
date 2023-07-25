/// <reference types="cypress" />

context('Grade', () => {
  before(() => {
      cy.task('loadDb')
  })

  it('lists grades', () => {
      cy.request('/grades').then(response => {
          expect(response.body['hydra:totalItems']).to.eq(22)
      })

      cy.request('/grades?pagination=false&order[name]=asc&exists[boulders]=true').then(response => {
        expect(response.body['hydra:totalItems']).to.eq(3)
        expect(response.body['hydra:member'][0].name).to.eq('5')
        expect(response.body['hydra:member'][1].name).to.eq('6a')
        expect(response.body['hydra:member'][2].name).to.eq('6c')

    })
  })

  it('gets a grade', () => {
      cy.request('/grades/1').then(response => {
          expect(response.body['name']).to.eq('4')
      })
  })
    
  it('cannot delete a grade', () => {
      cy.request({url :'/grades/1', method: 'DELETE', failOnStatusCode: false}).then(response => {
          expect(response.status).to.eq(405)
      })
  })
 
  it('cannot create a grade', () => {
      cy.request({url :'/grades', method: 'POST', body: {}, failOnStatusCode: false}).then(response => {
          expect(response.status).to.eq(405)
      })
  })

  it('cannot edit a grade', () => {
      cy.request({url :'/grades/1', method: 'PUT', body: {}, failOnStatusCode: false}).then(response => {
          expect(response.status).to.eq(405)
      })

      cy.request({url :'/grades/1', method: 'PATCH', body: {}, failOnStatusCode: false}).then(response => {
          expect(response.status).to.eq(405)
      })
  })

})
