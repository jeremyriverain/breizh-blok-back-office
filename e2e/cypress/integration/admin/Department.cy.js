/// <reference types="cypress" />

context("Department", () => {
  beforeEach(() => {
    cy.task("loadDb");
  });

  it("admins cannot access Department section", () => {
    cy.realLogin();
    cy.get("#main-menu").contains("Départements").should("not.exist");
  });
});

context("Department Super Admin", () => {
  beforeEach(() => {
    cy.task("loadDb");
    cy.realLogin("super-admin@fixture.com");
    cy.get("#main-menu").contains("Départements").click();
  });

  it("super admin can access Department section", () => {
    cy.get("table tbody tr").should("have.length", 1);
    cy.get("table tbody tr").first().should("contain.text", "Finistère");
  });

  it("super admin can delete Department", () => {
    cy.get("table tbody tr").should("have.length", 1);
    cy.get("table tbody tr").first().deleteRow();
    cy.get("table tbody").should("contain.text", "Aucun résultat trouvé");
    cy.get("#main-menu").contains("Communes").click();
    cy.get("table tbody tr").should("have.length", 2);
  });

  it("super admin can add department", () => {
    cy.contains("Créer Département").click();
    cy.get("h1").should("contain.text", 'Créer "Département"');
    cy.get("button.action-save").contains("Créer").click();
    cy.get("input[name=Department\\[name\\]]").should(
      "have.class",
      "is-invalid"
    );

    cy.get("input[name=Department\\[name\\]]").type("Morbihan");
    cy.get("button.action-save").contains("Créer").click();
    cy.get("table tbody tr").should("have.length", 2);
    cy.get("table tbody tr").should("contain.text", "Morbihan");
  });

  afterEach(() => {
    cy.logout();
  });
});
