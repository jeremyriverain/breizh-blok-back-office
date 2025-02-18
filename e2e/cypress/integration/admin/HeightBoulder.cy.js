/// <reference types="cypress" />

context("HeightBoulder", () => {
  beforeEach(() => {
    cy.task("loadDb");
  });

  it("users cannot access HeightBoulder section", () => {
    cy.realLogin();
    cy.get("#main-menu").contains("Hauteurs").should("not.exist");
  });
});

context("HeightBoulder Super Admin", () => {
  beforeEach(() => {
    cy.task("loadDb");
    cy.realLogin("super-admin@fixture.com");
    cy.get("#main-menu").contains("Hauteurs").click();
  });

  it("super admin can access HeightBoulder section", () => {
    cy.get("table tbody tr").should("have.length", 3);
  });

  it("super admin can delete HeightBoulder", () => {
    cy.get("table tbody tr").should("have.length", 3);
    cy.get("table tbody tr").first().deleteRow();
    cy.get("table tbody tr").should("have.length", 2);
    cy.get("table tbody tr").first().deleteRow();
    cy.get("table tbody tr").first().deleteRow();
    cy.get("table tbody").should("contain.text", "Aucun résultat trouvé");
    cy.get("#main-menu").contains("Blocs").click();
    cy.get("table tbody tr").should("have.length", 4);
  });

  it("super admin can add HeightBoulder", () => {
    cy.contains("Créer Hauteur").click();
    cy.get("h1").should("contain.text", 'Créer "Hauteur"');
    cy.get("button.action-save").contains("Créer").click();
    cy.get("input[name=HeightBoulder\\[min\\]]").should(
      "have.class",
      "is-invalid"
    );

    cy.get("input[name=HeightBoulder\\[min\\]]").type("0");
    cy.get("input[name=HeightBoulder\\[max\\]]").type("3");
    cy.get("button.action-save").contains("Créer").click();
    cy.contains("La combinaison des propriétés min et max existe déjà");
    cy.get("input[name=HeightBoulder\\[max\\]]").clear().type("4");
    cy.get("button.action-save").contains("Créer").click();
    cy.get("table tbody tr").should("have.length", 4);
  });

  afterEach(() => {
    cy.logout();
  });
});
