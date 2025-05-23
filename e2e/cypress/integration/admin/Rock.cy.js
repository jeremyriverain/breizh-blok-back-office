/// <reference types="cypress" />

context("Rock-read", () => {
  before(() => {
    cy.task("loadDb");
  });
  beforeEach(() => {
    cy.realLogin();
    cy.get("#main-menu").contains("Rochers").click();
  });

  it("list boulder areas", () => {
    cy.get("table tbody tr").should("have.length", 3);

    // lightbox is working. When I click on the thumbnail, a bigger image shows up
    cy.get(".basicLightbox").should("not.exist");
    cy.get("a.ea-lightbox-thumbnail").click();
    cy.get(".basicLightbox").should("exist");
  });

  it("show details", () => {
    cy.get("input[name=query]").type("Menez Ham").type("{enter}");
    cy.get("tr a.dropdown-toggle").first().takeAction("Consulter");
    cy.get("h1").should("contain.text", "Menez Ham #");
    cy.get("#map")
      .should("have.class", "leaflet-container")
      .get("img.leaflet-marker-icon");
    cy.get(".cy-boulders").contains("Blocs");
    cy.get(".cy-boulders").contains("Les cornes du diable");
  });
});

context("Rock-write as admin", () => {
  beforeEach(() => {
    cy.task("loadDb");
    cy.realLogin("admin@fixture.com");
    cy.get("#main-menu").contains("Rochers").click();
  });

  it("delete a boulder area", () => {
    cy.get("table tbody tr").first().deleteRow();
    cy.get("table tbody tr").should("have.length", 2);
  });

  it("create a rock", () => {
    cy.contains("Créer Rocher").click();
    cy.get("h1").should("contain.text", 'Créer "Rocher"');
    cy.get("select[name=Rock\\[boulderArea\\]]").chooseOption("Petit paradis");
    cy.get(".cy-pictures button").contains("Ajouter un nouvel élément").click();
    cy.fixture("boulder.jpg", { encoding: null }).as("boulder");
    cy.get('.cy-pictures input[type="file"]').selectFile({
      contents: "@boulder",
      fileName: "boulder.jpg",
    });

    cy.get("button.action-save").contains("Créer").click();
    cy.get("table tbody tr").should("have.length", 4);
    cy.get("table tbody tr")
      .first()
      .find(".cy-pictures img")
      .should("be.visible");
    cy.get("table tbody tr")
      .first()
      .find(".cy-pictures")
      .should("contain.text", "1");
  });

  it("edit a rock", () => {
    cy.get("tr a.dropdown-toggle").last().takeAction("Consulter");
    cy.get("#map")
      .should("have.class", "leaflet-container")
      .get("img.leaflet-marker-icon");
    cy.get("[data-cy=longitude]").then(($el) => {
      const lng = $el.text();
      cy.log(`initial longitude: ${lng}`);
      expect(lng.length).to.greaterThan(0);
      cy.get("a.action-edit").contains("Modifier").click();
      cy.get("input#Rock_location_longitude").clear().type(4);
      cy.get("button").contains("Sauvegarder les modifications").click();
      cy.get("tr a.dropdown-toggle").last().takeAction("Consulter");

      cy.get("[data-cy=longitude]").should(($el2) => {
        expect($el2.text()).eq("4");
      });
    });
    cy.get("[data-cy=latitude]").then(($el) => {
      const lat = $el.text();
      cy.log(`initial latitude: ${lat}`);
      expect(lat.length).to.greaterThan(0);

      cy.get("a.action-edit").contains("Modifier").click();
      cy.get("img.leaflet-marker-icon")
        .trigger("mousedown", { which: 1 })
        .trigger("mousemove", { clientX: 600 })
        .trigger("mouseup", { force: true });
      cy.get("button").contains("Sauvegarder les modifications").click();
      cy.get("tr a.dropdown-toggle").last().takeAction("Consulter");

      cy.get("[data-cy=latitude]").should(($el2) => {
        expect($el2.text()).not.to.eq(lat);
      });
    });
  });
});

context("Rock-write as contributor", () => {
  beforeEach(() => {
    cy.task("loadDb");
    cy.realLogin();
    cy.get("#main-menu").contains("Rochers").click();

    cy.contains("Créer Rocher").click();
    cy.get("h1").should("contain.text", 'Créer "Rocher"');
    cy.get("select[name=Rock\\[boulderArea\\]]").chooseOption("Petit paradis");
    cy.get(".cy-pictures button").contains("Ajouter un nouvel élément").click();
    cy.fixture("boulder.jpg", { encoding: null }).as("boulder");
    cy.get('.cy-pictures input[type="file"]').selectFile({
      contents: "@boulder",
      fileName: "boulder.jpg",
    });

    cy.get("button.action-save").contains("Créer").click();
    cy.get("table tbody tr").should("have.length", 4);
  });

  it("cannot delete or edit a rock are that does not belong to me", () => {
    cy.get("input[name=query]").type("Cremiou").type("{enter}");
    cy.get("table tbody tr")
      .first()
      .find("a.dropdown-toggle")
      .click()
      .next()
      .find("a")
      .should("have.length", 1);
    cy.get("table tbody tr").first().find("a.dropdown-toggle").click();
    cy.get("tr a.dropdown-toggle").first().takeAction("Consulter");
  });

  it("can delete a rock created by me", () => {
    cy.get("input[name=query]").type("Petit paradis").type("{enter}");
    cy.get("table tbody tr").first().deleteRow();
    cy.get("input[name=query]").clear().type("{enter}");
    cy.get("table tbody tr").should("have.length", 3);
  });

  it("can edit a rock created by me", () => {
    cy.get("input[name=query]").type("Petit paradis").type("{enter}");
    cy.get("tr a.dropdown-toggle").first().takeAction("Modifier");
    cy.contains("Modifier Rocher");
  });
});
