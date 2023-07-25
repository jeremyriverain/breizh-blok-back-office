/// <reference types="cypress" />

context('Boulder', () => {
  before(() => {
      cy.task('loadDb')
  })

  it('lists boulders', () => {
      cy.request('/boulders').then(response => {
          expect(response.body['hydra:totalItems']).to.eq(4)
          expect(response.body['hydra:member'][0].name).not.to.be.empty
          expect(response.body['hydra:member'][0].grade.name).not.to.be.empty
          expect(response.body['hydra:member'][0].rock.location.latitude).not.to.be.empty
          expect(response.body['hydra:member'][0].rock.location.longitude).not.to.be.empty
          expect(response.body['hydra:member'][0].rock.location.longitude).not.to.be.empty
          expect(response.body['hydra:member'][0].rock.boulderArea.name).not.to.be.empty
          expect(response.body['hydra:member'][0].rock.boulderArea.municipality.name).not.to.be.empty
          expect(response.body['hydra:member'][0].description).to.be.undefined
          expect(response.body['hydra:member'][0].lineBoulders[0].rockImage.filterUrl).to.contain('%filter%')
          expect(response.body['hydra:member'][0].lineBoulders[0].rockImage.contentUrl).not.to.be.empty
      })
 
      cy.request({
          url: '/boulders?pagination=false&groups[]=Boulder:map',
          headers: {
              accept: 'application/json'
            }
        })
        .its('body')
        .should('deep.equal', [
            {
                "id": 1,
                "rock": {
                "location": {
                    "latitude": "48.673149748436",
                    "longitude": "-4.3580819451625"
                }
                }
            },
            {
                "id": 2,
                "rock": {
                "location": {
                    "latitude": "48.673149748436",
                    "longitude": "-4.3580819451625"
                }
                }
            },
            {
                "id": 3,
                "rock": {
                "location": {
                    "latitude": "48.673314470371",
                    "longitude": "-4.357883461703"
                }
                }
            },
            {
                "id": 4,
                "rock": {
                "location": {
                    "latitude": "48.66945913666",
                    "longitude": "-4.3719220691165"
                }
                }
            }
            ])
  })

  it('gets a boulder', () => {
      cy.request('/boulders/1').then(response => {
          expect(response.body.name).not.to.be.empty
          expect(response.body.grade.name).not.to.be.empty
          expect(response.body.rock.location.latitude).not.to.be.empty
          expect(response.body.rock.location.longitude).not.to.be.empty
          expect(response.body.description).not.to.be.empty
          expect(response.body.lineBoulders[0].rockImage.filterUrl).to.contain('%filter%')
          expect(response.body.lineBoulders[0].rockImage.contentUrl).not.to.be.empty
      })
  })
    
  it('cannot delete a boulder', () => {
      cy.request({url :'/boulders/1', method: 'DELETE', failOnStatusCode: false}).then(response => {
          expect(response.status).to.eq(405)
      })
  })
 
  it('cannot create a boulder', () => {
      cy.request({url :'/boulders', method: 'POST', body: {}, failOnStatusCode: false}).then(response => {
          expect(response.status).to.eq(405)
      })
  })

  it('cannot edit a boulder', () => {
      cy.request({url :'/boulders/1', method: 'PUT', body: {}, failOnStatusCode: false}).then(response => {
          expect(response.status).to.eq(405)
      })

      cy.request({url :'/boulders/1', method: 'PATCH', body: {}, failOnStatusCode: false}).then(response => {
          expect(response.status).to.eq(405)
      })
  })

  it('can search boulders by entering their name', () => {
      cy.request({url :'/boulders?term=Onk', method: 'GET'}).then(response => {
          expect(response.body['hydra:totalItems']).to.eq(1)
          expect(response.body['hydra:member'][0].name).to.eq('Monkey')
      })
  })

  it('can search boulders by entering a boulder area', () => {
      cy.request({url :'/boulders?term=cre', method: 'GET'}).then(response => {
          expect(response.body['hydra:totalItems']).to.eq(3)
          cy.wrap(response.body['hydra:member']).each(item => {
              cy.wrap(item.rock.boulderArea.name).should('eq', 'Cremiou')
          })
      })
  })

   it('can search boulders by entering a municipality', () => {
      cy.request({url :'/boulders?term=ker', method: 'GET'}).then(response => {
          expect(response.body['hydra:totalItems']).to.eq(4)
          cy.wrap(response.body['hydra:member']).each(item => {
              cy.wrap(item.rock.boulderArea.municipality.name).should('eq', 'Kerlouan')
          })
      })

      cy.request({url :'/boulders?term=plaintel', method: 'GET'}).then(response => {
          expect(response.body['hydra:totalItems']).to.eq(0)
      })
  })

})
