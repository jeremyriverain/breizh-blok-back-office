/// <reference types="cypress" />

context("Auth", () => {
  before(() => {
    cy.task("loadDb");
  });

  it("authentication works", () => {
    cy.visit("/admin");
    cy.url().should("match", /\/login/);

    cy.realLogin("fake-user@fixture.com");
    cy.visit("/admin");
    cy.url().should("match", /\/login/);

    cy.realLogin();
    cy.url().should("not.match", /\/login/);
  });

  it("user cannot access the dashboard", () => {
    cy.realLogin("user@fixture.com");
    cy.contains("Access Denied.");
  });

  it("updates profile", () => {
    const emailSelector = "input[name=User\\[email\\]]";
    const newEmail = "cy-test@fixture.com";
    cy.realLogin();
    cy.visit("/admin");
    cy.get("aside.content-top").contains("contributor@fixture.com").click();
    cy.get("a").contains("Mon profil").click({ force: true });
    cy.get("h1").contains("Modifier Utilisateur");

    cy.get(emailSelector).clear().type(newEmail);
    cy.get("button").contains("Sauvegarder").click();
    cy.logout();
    cy.visit("/admin");
    cy.url().should("match", /\/login/);

    cy.realLogin();
    cy.visit("/admin");
    cy.url().should("match", /\/login/);

    cy.realLogin(newEmail);
    cy.url().should("not.match", /\/login/);
  });
});
