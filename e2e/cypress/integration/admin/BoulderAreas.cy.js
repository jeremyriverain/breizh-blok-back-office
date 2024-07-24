/// <reference types="cypress" />

context("BoulderArea-read", () => {
  before(() => {
    cy.task("loadDb");
  });
  beforeEach(() => {
    cy.realLogin();
  });

  it("list boulder areas", () => {
    cy.get("table tbody tr").should("have.length", 6);
    cy.get("table tbody tr").first().should("contain.text", "Bivouac");
    cy.get("table tbody tr").last().should("contain.text", "Petit paradis");

    cy.get("input[name=query]").type("Cremiou").type("{enter}");
    cy.get("table tbody tr")
      .first()
      .should("contain.text", "Cremiou")
      .get(".cy-boulders")
      .should("contain.text", "3");
  });

  it("show details", () => {
    cy.get("table tbody tr")
      .contains("Cremiou")
      .closest("tr")
      .find("a.dropdown-toggle")
      .first()
      .takeAction("Consulter");
    cy.get("h1").should("contain.text", "Cremiou");
    cy.get(".cy-boulders tbody tr").should("have.length", 3).contains("Stone");
    cy.get("#map")
      .should("have.class", "leaflet-container")
      .get("img.leaflet-marker-icon");
  });
});

context("BoulderArea-write", () => {
  beforeEach(() => {
    cy.task("loadDb");
    cy.realLogin();
  });

  it("delete a boulder area", () => {
    cy.get("table tbody tr").first().should("contain.text", "Bivouac");
    cy.get("table tbody tr").first().deleteRow();
    cy.get("table tbody tr").first().should("not.contain.text", "Bivouac");
  });

  it("create a boulder area", () => {
    cy.contains("Créer Secteur").click();
    cy.get("h1").should("contain.text", 'Créer "Secteur"');
    cy.get("button.action-save").contains("Créer").click();
    cy.get("input[name=BoulderArea\\[name\\]]").should(
      "have.class",
      "is-invalid"
    );

    cy.get("input[name=BoulderArea\\[name\\]]").type("SNSM");
    cy.get("select[name=BoulderArea\\[municipality\\]]").chooseOption(
      "Kerlouan"
    );
    cy.get("button.action-save").contains("Créer").click();
    cy.get("table tbody tr").last().should("contain.text", "SNSM");
    cy.get("table tbody tr").last().should("contain.text", "Kerlouan");
  });

  it("does not fill parking location by default", () => {
    cy.contains("Créer Secteur").click();
    cy.get("h1").should("contain.text", 'Créer "Secteur"');
    cy.get("[data-cy=geo-point-field-toggler-btn]").contains("Ajouter");
    cy.get("[data-cy=geo-point-field-map-box]").should("not.exist");
    cy.get("input[name=BoulderArea\\[name\\]]").type("Rocher Margot");
    cy.get("button.action-save").contains("Créer").click();
    cy.get("table tbody tr").should("have.length", 7);
    cy.get("td")
      .contains("Rocher Margot")
      .closest("tr")
      .find('[data-label="Emplacement parking"]')
      .should("contain", "Aucun");
  });

  context("fills parking location correctly", () => {
    it("scenario 1", () => {
      // add a boulder area and fill in the parking location
      cy.contains("Créer Secteur").click();
      cy.get("h1").should("contain.text", 'Créer "Secteur"');
      cy.get("[data-cy=geo-point-field-map-box]").should("not.exist");
      cy.get("input[name=BoulderArea\\[name\\]]").type("Rocher Margot");
      cy.get("[data-cy=geo-point-field-toggler-btn]").contains("Ajouter");
      cy.get("[data-cy=geo-point-field-toggler-btn]").click();
      cy.get("[data-cy=geo-point-field-map-box]");
      cy.get("[data-cy=geo-point-field-toggler-btn]").contains("Supprimer");
      cy.get("button.action-save").contains("Créer").click();
      cy.get("table tbody tr").should("have.length", 7);
      cy.get("td")
        .contains("Rocher Margot")
        .closest("tr")
        .find('[data-label="Emplacement parking"]')
        .should("not.contain", "Aucun");

      // edit the same boulder area and fill in an other parking location
      cy.get("td")
        .contains("Rocher Margot")
        .closest("tr")
        .find('[data-label="Emplacement parking"]')
        .then(($el) => {
          const latLng = $el.text();
          cy.log(latLng);
          expect(latLng.length).to.greaterThan(0);

          cy.get("table tbody tr")
            .contains("Rocher Margot")
            .closest("tr")
            .find("a.dropdown-toggle")
            .first()
            .takeAction("Éditer");
          cy.get("[data-cy=geo-point-field-map-box]");
          cy.get("[data-cy=geo-point-field-toggler-btn]").contains("Supprimer");

          cy.get("img.leaflet-marker-icon")
            .trigger("mousedown", { which: 1 })
            .trigger("mousemove", { clientX: 600 })
            .trigger("mouseup", { force: true });
          cy.get("button").contains("Sauvegarder les modifications").click();

          cy.get("td")
            .contains("Rocher Margot")
            .closest("tr")
            .find('[data-label="Emplacement parking"]')
            .should(($el2) => {
              expect($el2.text()).not.to.eq(latLng);
            });
        });

      // delete the parking location of this boulder area
      cy.get("table tbody tr")
        .contains("Rocher Margot")
        .closest("tr")
        .find("a.dropdown-toggle")
        .first()
        .takeAction("Éditer");

      cy.get("[data-cy=geo-point-field-toggler-btn]")
        .contains("Supprimer")
        .click();
      cy.get("[data-cy=geo-point-field-map-box]").should("not.exist");
      cy.get("button").contains("Sauvegarder les modifications").click();

      cy.get("td")
        .contains("Rocher Margot")
        .closest("tr")
        .find('[data-label="Emplacement parking"]')
        .should("contain", "Aucun");
    });

    it("scenario 2", () => {
      // add a boulder area and fill in the parking location
      cy.contains("Créer Secteur").click();
      cy.get("h1").should("contain.text", 'Créer "Secteur"');
      cy.get("[data-cy=geo-point-field-map-box]").should("not.exist");
      cy.get("input[name=BoulderArea\\[name\\]]").type("Rocher Margot");
      // toggles several times the display of the map to check the correct hydration of the inputs
      cy.get("[data-cy=geo-point-field-toggler-btn]")
        .contains("Ajouter")
        .click();
      cy.get("[data-cy=geo-point-field-toggler-btn]")
        .contains("Supprimer")
        .click();
      cy.get("[data-cy=geo-point-field-toggler-btn]")
        .contains("Ajouter")
        .click();
      cy.get("[data-cy=geo-point-field-toggler-btn]")
        .contains("Supprimer")
        .click();
      cy.get("[data-cy=geo-point-field-toggler-btn]")
        .contains("Ajouter")
        .click();
      cy.get("button.action-save").contains("Créer").click();
      cy.get("table tbody tr").should("have.length", 7);
      cy.get("td")
        .contains("Rocher Margot")
        .closest("tr")
        .find('[data-label="Emplacement parking"]')
        .should("not.contain", "Aucun");
    });
  });
});
