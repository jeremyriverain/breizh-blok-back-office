/// <reference types="cypress" />

context("BoulderArea", () => {
  before(() => {
    cy.task("loadDb");
  });

  it("lists boulder areas", () => {
    cy.request("/boulder_areas").then((response) => {
      expect(response.body["hydra:totalItems"]).to.eq(6);
      expect(response.body["hydra:member"][0]["name"]).not.to.be.empty;
      expect(response.body["hydra:member"][0]["parkingLocation"]).to.be.undefined;
      cy.wrap(
        response.body["hydra:member"][0]["numberOfBouldersGroupedByGrade"]
      ).should("be.undefined");
    });
  });

  it("gets a boulder area", () => {
    cy.request("/boulder_areas/1").then((response) => {
      expect(response.body["name"]).to.eq("Cremiou");
      expect(response.body["municipality"]["name"]).to.eq("Kerlouan");
      cy.wrap(response.body["numberOfBouldersGroupedByGrade"]).should(
        "contain",
        { 5: 1, "6a": 1, "6c": 1 }
      );
    });
  });

  it("parking location is returned if not null", () => {
    cy.request("/boulder_areas/1").then((response) => {
      expect(response.body["name"]).to.eq("Cremiou");
      expect(response.body["parkingLocation"]['longitude']).not.to.be.empty;
      expect(response.body["parkingLocation"]['latitude']).not.to.be.empty;
    });
  });

  it("parking location is undefined if it does not exist", () => {
    cy.request("/boulder_areas/2").then((response) => {
      expect(response.body["name"]).to.eq("Petit paradis");
      expect(response.body["parkingLocation"]).to.be.undefined;
    });
  });

  it("cannot delete a boulder area", () => {
    cy.request({
      url: "/boulder_areas/1",
      method: "DELETE",
      failOnStatusCode: false,
    }).then((response) => {
      expect(response.status).to.eq(405);
    });
  });

  it("cannot create a boulder area", () => {
    cy.request({
      url: "/boulder_areas",
      method: "POST",
      body: {},
      failOnStatusCode: false,
    }).then((response) => {
      expect(response.status).to.eq(405);
    });
  });

  it("cannot edit a boulder area", () => {
    cy.request({
      url: "/boulder_areas/1",
      method: "PUT",
      body: {},
      failOnStatusCode: false,
    }).then((response) => {
      expect(response.status).to.eq(405);
    });

    cy.request({
      url: "/boulder_areas/1",
      method: "PATCH",
      body: {},
      failOnStatusCode: false,
    }).then((response) => {
      expect(response.status).to.eq(405);
    });
  });
});
