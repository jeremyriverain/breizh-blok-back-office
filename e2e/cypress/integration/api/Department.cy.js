/// <reference types="cypress" />

context("Department", () => {
  before(() => {
    cy.task("loadDb");
  });

  it("lists departments", () => {
    cy.request("/departments").then((response) => {
      expect(response.body["hydra:totalItems"]).to.eq(1);
    });

    cy.request(
      "/departments?exists[municipalities.boulderAreas.rocks.boulders]=true"
    ).then((response) => {
      const items = response.body["hydra:member"];
      expect(response.body["hydra:totalItems"]).to.eq(1);
      cy.wrap(items[0].name).should("contain", "Finistère");
      cy.wrap(items[0].municipalities.length).should("eq", 1);
      cy.wrap(items[0].municipalities[0].boulderAreas.length).should("eq", 2);
    });
  });

  it("gets a department", () => {
    cy.request("/departments/1").then((response) => {
      cy.wrap(response.body["name"]).should("contain", "Finistère");
    });
  });

  it("cannot delete a department", () => {
    cy.request({
      url: "/departments/1",
      method: "DELETE",
      failOnStatusCode: false,
    }).then((response) => {
      expect(response.status).to.eq(405);
    });
  });

  it("cannot create a department", () => {
    cy.request({
      url: "/departments",
      method: "POST",
      body: {},
      failOnStatusCode: false,
    }).then((response) => {
      expect(response.status).to.eq(405);
    });
  });

  it("cannot edit a department", () => {
    cy.request({
      url: "/departments/1",
      method: "PUT",
      body: {},
      failOnStatusCode: false,
    }).then((response) => {
      expect(response.status).to.eq(405);
    });

    cy.request({
      url: "/departments/1",
      method: "PATCH",
      body: {},
      failOnStatusCode: false,
    }).then((response) => {
      expect(response.status).to.eq(405);
    });
  });
});
