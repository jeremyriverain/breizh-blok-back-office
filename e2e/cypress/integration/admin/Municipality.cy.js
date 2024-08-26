/// <reference types="cypress" />

context("Municipality as admin", () => {
  before(() => {
    cy.task("loadDb");
  });
  beforeEach(() => {
    cy.realLogin();
    cy.get("#main-menu").contains("Communes").click();
  });

  it("list boulder areas", () => {
    cy.get("table tbody tr").should("have.length", 2);
    cy.get("table tbody tr").first().should("contain.text", "Kerlouan");
    cy.get("table tbody tr").first().should("contain.text", "Finistère");
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

  it("user cannot create, delete or edit municipalities", () => {
    cy.get("table tbody tr")
      .first()
      .find("a.dropdown-toggle")
      .click()
      .next()
      .find("a")
      .should("have.length", 1)
      .contains("Consulter");
    cy.contains("Créer Commune").should("not.exist");
  });
});

context("Municipality as super admin", () => {
  beforeEach(() => {
    cy.task("loadDb");
    cy.realLogin("super-admin@fixture.com");
    cy.get("#main-menu").contains("Communes").click();
  });

  it("deletes a municipality", () => {
    cy.get("table tbody tr").should("have.length", 2);
    cy.get("table tbody tr").first().deleteRow();
    cy.get("table tbody tr").should("have.length", 1);
  });

  it("create a municipality", () => {
    cy.contains("Créer Commune").click();
    cy.get("h1").should("contain.text", 'Créer "Commune"');
    cy.get("button.action-save").contains("Créer").click();
    cy.get("input[name=Municipality\\[name\\]]").should(
      "have.class",
      "is-invalid"
    );

    cy.get("input[name=Municipality\\[name\\]]").type("Kerlouan");
    cy.get("select[name=Municipality\\[department\\]]").chooseOption(
      "Finistère"
    );
    cy.get("button.action-save").contains("Créer").click();
    cy.get("input[name=Municipality\\[name\\]]")
      .next()
      .get(".invalid-feedback")
      .should("contain.text", "Cette valeur est déjà utilisée");

    cy.get("input[name=Municipality\\[name\\]]")
      .clear()
      .type("Locmaria-Plouzané");
    cy.get("button.action-save").contains("Créer").click();

    cy.get("table tbody tr").should("have.length", 3);
  });
});
