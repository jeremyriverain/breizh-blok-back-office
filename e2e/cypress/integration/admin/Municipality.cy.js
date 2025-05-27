/// <reference types="cypress" />

context("Municipality as admin", () => {
  before(() => {
    cy.task("loadDb");
  });
  beforeEach(() => {
    cy.realLogin();
    cy.get("#main-menu").contains("Communes").click();
  });

  it("show details", () => {
    cy.get("table tbody tr")
      .contains("Kerlouan")
      .closest("tr")
      .find("a.dropdown-toggle")
      .first()
      .takeAction("Consulter");
    cy.get("h1").should("contain.text", "Kerlouan");
    cy.get(".cy-boulderAreas").contains("Secteurs");
    cy.get(".cy-boulderAreas")
      .find("tbody tr")
      .should("have.length", 5)
      .should("contain.text", "Cremiou");
    cy.get("#map")
      .should("have.class", "leaflet-container")
      .get("img.leaflet-marker-icon");
  });

});
