
/// <reference types="cypress" />

context('Boulders', () => {
  beforeEach(() => {
    cy.task('loadDb')
    cy.realLogin()
    cy.get('#main-menu').contains('Blocs').click()
  })

  it('list boulder', () => {
    cy.get('table tbody tr').should('have.length', 4)
  })

  it('delete a boulder', () => {
    cy.get('table tbody tr').first().deleteRow()
    cy.get('table tbody tr').should('have.length', 3)
  })

  it('filters boulders', () => {

    cy.get('table tbody tr').should('have.length', 4)
    cy.get('a.action-filters-button').click()
    cy.get('[data-filter-property=boulderArea] input[type=checkbox]').check()
    cy.get('select[name=filters\\[boulderArea\\]]').find('option').should('have.length', 2)
    cy.get('select[name=filters\\[boulderArea\\]]').select('Menez Ham')
    cy.get('#modal-filters button#modal-apply-button').click()
    cy.get('table tbody tr').should('have.length', 1)
  })

  it('show details', () => {
    cy.get('tr a.dropdown-toggle').last().takeAction('Consulter')
    cy.get('h1').should('contain.text', 'Stone')
    cy.get('#map').should('have.class', 'leaflet-container').get('img.leaflet-marker-icon')
    cy.get('.drawing-container svg path').invoke('attr', 'd').should('match', /^M474/)
  })

  it('create a boulder', () => {
    cy.contains('Créer Bloc').click()
    cy.get('h1').should('contain.text', 'Créer "Bloc"')
    cy.get('button.action-save').contains('Créer').click()
    cy.get('input[name=Boulder\\[name\\]]').should('have.class', 'is-invalid')

    cy.get('input[name=Boulder\\[name\\]]').type('La route du rhum')
    cy.get('select[name=Boulder\\[rock\\]]').chooseOption('Menez Ham')

    cy.get('button.action-save').contains('Créer').click()
    cy.get('table tbody tr').should('have.length', 5)
  })

  it('cannot draw line if no picture associated to the rock', () => {
    cy.get('tr a.dropdown-toggle').first().takeAction('Consulter')
    cy.get('a').contains('Ligne du bloc').click()
    cy.get('h1').contains('Ligne du bloc')
    cy.get('p').contains("Vous ne pouvez pas dessiner la ligne de bloc car aucune photo n'est associée au rocher.")
    cy.get('a').contains('Ajouter des photos').click()
    cy.get('h1').contains('Modifier Rocher')
  })

  it('can draw line', () => {
    cy.get('tr a.dropdown-toggle').last().takeAction('Ligne du bloc')

    cy.get('img[data-cy=drawing-image]')
      .should('be.visible')
      .invoke('width').should('eq', 550)
    cy.get('img[data-cy=drawing-image]').then(img => {
      expect(Math.ceil(img.height())).to.eq(367)
    })

    cy.get('.drawing-container svg')
      .should('be.visible')
      .invoke('attr', 'width').should('eq', '550')

    cy.get('.drawing-container svg')
      .then(svgEl => {
        expect(Math.ceil(svgEl.height())).to.eq(367)
      })

    cy.intercept('DELETE', '/admin/line_boulders/*').as('deleteLineBoulder')
    cy.get('[aria-label=clear]').click()
    cy.get('[aria-label=save]').click()
    cy.wait('@deleteLineBoulder').its('response.statusCode').should('eq', 204)

    
    cy.get('.drawing-container svg')
      .realMouseDown()
      .realMouseMove(100, 100, { position: "center" })

    cy.intercept('POST', '/admin/line_boulders').as('postLineBoulder')
    cy.get('[aria-label=save]').click()
    cy.wait('@postLineBoulder').then(interception => {
      const lineBoulderIri = interception.response.body['@id']
      expect(interception.response.statusCode).to.eq(201)
      expect(interception.request.body).to.have.property('boulder')
      expect(interception.request.body).to.have.property('rockImage')
      expect(interception.request.body.arrArrPoints).to.have.length(1)
      expect(interception.request.body.arrArrPoints[0][0].x).to.be.a('number')
      expect(interception.request.body.arrArrPoints[0][0].y).to.be.a('number')
      
      cy.get('.drawing-container svg')
      .realMouseDown()
      .realMouseMove(50, 200, { position: "right" })

      cy.get('.drawing-container svg')
      .realMouseDown()
      .realMouseMove(250, 300, { position: "bottom" })

      cy.get('[aria-label=undo]').click()

      cy.intercept('PUT', lineBoulderIri).as('editLineBoulder')
      cy.get('[aria-label=save]').click()
      cy.wait('@editLineBoulder').then(interception => {
        expect(interception.response.statusCode).to.eq(200)
        expect(interception.request.body).not.to.have.property('boulder')
        expect(interception.request.body).not.to.have.property('rockImage')
        expect(interception.request.body.arrArrPoints).to.have.length(2)
      })


    })
   
  })

})
