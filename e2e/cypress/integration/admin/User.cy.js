/// <reference types="cypress" />

context("User", () => {
  beforeEach(() => {
    cy.task("loadDb");
  });

  it("users cannot access User section", () => {
    cy.realLogin();
    cy.get("#main-menu").contains("Utilisateurs").should("not.exist");
  });
});

context("User Super Admin", () => {
  beforeEach(() => {
    cy.task("loadDb");
    cy.realLogin("super-admin@fixture.com");
    cy.get("#main-menu").contains("Utilisateurs").click();
  });

  it("super admin can access User section", () => {
    cy.get("table tbody tr").should("have.length", 4);
    cy.get("table tbody tr").last().should("contain.text", "user@fixture.com");
  });

  it("super admin can delete user", () => {
    cy.get("table tbody tr").should("have.length", 4);
    cy.get("table tbody tr").first().deleteRow();
    cy.get("table tbody tr").should("have.length", 3);
  });

  it("super admin can add contributor", () => {
    cy.contains("Créer Utilisateur").click();
    cy.get("h1").should("contain.text", 'Créer "Utilisateur"');
    cy.get("button.action-save").contains("Créer").click();
    cy.get("input[name=User\\[email\\]]").should("have.class", "is-invalid");

    cy.get("input[name=User\\[email\\]]").type("test@fixture.com");
    cy.contains("ROLE_CONTRIBUTOR").click();
    cy.get("button.action-save").contains("Créer").click();
    cy.get("table tbody tr").should("have.length", 5);
    cy.get("table tbody tr").should("contain.text", "test@fixture.com");

    cy.logout();
    cy.realLogin("test@fixture.com");
    cy.get("#main-menu").contains("Utilisateurs").should("not.exist");
    cy.get("#main-menu").contains("Secteurs");
  });

  afterEach(() => {
    cy.logout();
  });
});
