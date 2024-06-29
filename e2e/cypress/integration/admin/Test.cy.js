/// <reference types="cypress" />

context("Test", () => {
  beforeEach(() => {
    cy.task("loadDb");
  });

  it("cloud storage works", () => {
    cy.request("/test/cloud-storage").then((response) => {
      expect(response.body["success"]).to.contain("created");
    });
  });
});
