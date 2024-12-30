/// <reference types="cypress" />

context("LineBoulder-read", () => {
  before(() => {
    cy.task("loadDb");
  });

  it("lists line boulders", () => {
    cy.realLogin();

    cy.request("/admin/line_boulders").then((response) => {
      expect(response.body["hydra:totalItems"]).to.eq(2);
    });
  });

  it("gets a line boulder", () => {
    cy.realLogin();

    cy.request("/admin/line_boulders/1").then((response) => {
      expect(response.body.arrArrPoints).not.eq(null);
      expect(response.body.smoothLine).not.eq(null);
      expect(response.body.rockImage["@id"]).to.exist;
    });
  });
});

context("LineBoulder-write as admin", () => {
  beforeEach(() => {
    cy.task("loadDb");
  });

  it("deletes a line boulder", () => {
    cy.realLogin("admin@fixture.com");

    cy.request({ method: "DELETE", url: "/admin/line_boulders/1" }).then(
      (response) => {
        expect(response.status).to.eq(204);
      }
    );
  });

  it("creates a line boulder", () => {
    cy.realLogin("admin@fixture.com");

    cy.request({
      method: "POST",
      url: "/admin/line_boulders",
      body: {},
      failOnStatusCode: false,
    }).then((response) => {
      expect(response.status).to.eq(422);
      expect(response.body["hydra:description"]).to.contain(
        "boulder: Cette valeur ne doit pas être vide."
      );
      expect(response.body["hydra:description"]).to.contain(
        "smoothLine: Cette valeur ne doit pas être vide."
      );
      expect(response.body["hydra:description"]).to.contain(
        "arrArrPoints: Cette valeur ne doit pas être vide."
      );
    });

    cy.request({
      method: "POST",
      url: "/admin/line_boulders",
      body: {
        boulder: "/boulders/3",
        rockImage: "/media/1",
      },
      failOnStatusCode: false,
    }).then((response) => {
      expect(response.status).to.eq(422);
      expect(response.body["hydra:description"]).to.contain(
        "boulder: This boulder does not match with its rock associated"
      );
    });

    cy.request({ method: "DELETE", url: "/admin/line_boulders/1" });

    cy.request({
      method: "POST",
      url: "/admin/line_boulders",
      body: {
        boulder: "/boulders/1",
        rockImage: "/media/1",
        arrArrPoints: [[]],
        smoothLine: "M",
      },
      failOnStatusCode: false,
    }).then((response) => {
      expect(response.status).to.eq(201);
    });
  });

  it("edits a line boulder", () => {
    cy.realLogin("admin@fixture.com");

    cy.request({
      method: "PUT",
      url: "/admin/line_boulders/1",
      body: {
        rockImage: "/media/2", // check I cant update this field
        boulder: "/boulders/2", // check I cant update this field
      },
      failOnStatusCode: false,
    }).then((response) => {
      expect(response.status).to.eq(200);
      expect(response.body.boulder["@id"]).to.eq("/boulders/1");
      expect(response.body.rockImage["@id"]).to.eq("/media/1");
    });
  });
});

context("LineBoulder-write as contributor", () => {
  beforeEach(() => {
    cy.task("loadDb");
    cy.realLogin();
    cy.get("#main-menu").contains("Blocs").click();
    cy.contains("Créer Bloc").click();
    cy.get("h1").should("contain.text", 'Créer "Bloc"');
    cy.get("button.action-save").contains("Créer").click();
    cy.get("input[name=Boulder\\[name\\]]").type("La route du rhum");
    cy.get("select[name=Boulder\\[rock\\]]").chooseOption("Cremiou");

    cy.get("button.action-save").contains("Créer").click();
    cy.get("table tbody tr").should("have.length", 5);
  });

  it("cannot delete a line boulder for a boulder not created by me", () => {
    cy.request({
      method: "DELETE",
      url: "/admin/line_boulders/1",
      failOnStatusCode: false,
    }).then((response) => {
      expect(response.status).to.eq(403);
    });
  });

  it("cannot edit a line boulder for a boulder not created by me", () => {
    cy.request({
      method: "PUT",
      url: "/admin/line_boulders/1",
      body: {},
      failOnStatusCode: false,
    }).then((response) => {
      expect(response.status).to.eq(403);
    });
  });

  it("creates, edits and deletes a line boulder associated to a boulder created by me", () => {
    cy.request({
      method: "POST",
      url: "/admin/line_boulders",
      body: {
        boulder: "/boulders/5",
        rockImage: "/media/1",
        arrArrPoints: [[]],
        smoothLine: "M",
      },
      failOnStatusCode: false,
    }).then((response) => {
      expect(response.status).to.eq(201);
    });

    cy.request({
      method: "PUT",
      url: "/admin/line_boulders/3",
      body: {},
      failOnStatusCode: false,
    }).then((response) => {
      expect(response.status).to.eq(200);
    });

    cy.request({
      method: "DELETE",
      url: "/admin/line_boulders/3",
      failOnStatusCode: false,
    }).then((response) => {
      expect(response.status).to.eq(204);
    });
  });
});
