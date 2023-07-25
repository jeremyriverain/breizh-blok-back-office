/// <reference types="cypress" />

context('Municipality', () => {
  before(() => {
      cy.task('loadDb')
  })

  it('lists municipalities', () => {
      cy.request('/municipalities').then(response => {
          expect(response.body['hydra:totalItems']).to.eq(2)
      })
  })

  it('gets a municipality', () => {
      cy.request('/municipalities/2').then(response => {
          expect(response.body['name']).to.eq('Kerlouan')
          expect(parseFloat(response.body.centroid.latitude)).to.gt(48)
          expect(parseFloat(response.body.centroid.longitude)).to.lt(0)
          expect(response.body.boulderAreas.length).to.eq(5)

          const bivouac = response.body.boulderAreas[0]
          expect(bivouac.numberOfBoulders).to.eq(0)
          expect(bivouac.lowestGrade).null
          expect(bivouac.highestGrade).null

          const cremiou = response.body.boulderAreas[1]
          expect(cremiou.centroid.latitude).not.null
          expect(cremiou.centroid.longitude).not.null
          expect(cremiou.numberOfBoulders).to.eq(3)
          expect(cremiou.lowestGrade.name).to.eq('5')
          expect(cremiou.highestGrade.name).to.eq('6c')
      })
  })
    
  it('cannot delete municipality', () => {
      cy.request({url :'/municipalities/1', method: 'DELETE', failOnStatusCode: false}).then(response => {
          expect(response.status).to.eq(405)
      })
  })
 
  it('cannot create a municipality', () => {
      cy.request({url :'/municipalities', method: 'POST', body: {}, failOnStatusCode: false}).then(response => {
          expect(response.status).to.eq(405)
      })
  })

  it('cannot edit a municipality', () => {
      cy.request({url :'/municipalities/1', method: 'PUT', body: {}, failOnStatusCode: false}).then(response => {
          expect(response.status).to.eq(405)
      })

      cy.request({url :'/municipalities/1', method: 'PATCH', body: {}, failOnStatusCode: false}).then(response => {
          expect(response.status).to.eq(405)
      })
  })

})
