/// <reference types="cypress" />

context("Boulders-read", () => {
  before(() => {
    cy.task("loadDb");
  });

  beforeEach(() => {
    cy.realLogin();
    cy.get("#main-menu").contains("Blocs").click();
  });

  it("list boulder", () => {
    cy.get("table tbody tr").should("have.length", 4);
    cy.get("table tbody tr").first().as("firstBoulder");
  });

  it("filters boulders", () => {
    cy.get("table tbody tr").should("have.length", 4);
    cy.get("a.action-filters-button").click();
    cy.get("[data-filter-property=boulderArea] input[type=checkbox]").check();
    cy.get("select[name=filters\\[boulderArea\\]]")
      .find("option")
      .should("have.length", 2);
    cy.get("select[name=filters\\[boulderArea\\]]").select("Menez Ham");
    cy.get("#modal-filters button#modal-apply-button").click();
    cy.get("table tbody tr").should("have.length", 1);
  });

  it("filters urban boulders", () => {
    cy.get("table tbody tr").should("have.length", 4);
    cy.get("a.action-filters-button").click();
    cy.get("[data-filter-property=isUrban] input[type=checkbox]").check();
    cy.get("input[name=filters\\[isUrban\\]]").should("have.length", 2);
    cy.get("input[name=filters\\[isUrban\\]]").check("1");
    cy.get("#modal-filters button#modal-apply-button").click();
    cy.get("table tbody tr").should("have.length", 1);
    cy.get("table tbody tr").first().should("contain.text", "Stone");
  });

  it("filters height boulders", () => {
    cy.get("table tbody tr").should("have.length", 4);
    cy.get("a.action-filters-button").click();
    cy.get("[data-filter-property=height] input[type=checkbox]").check();
    cy.get("select[name=filters\\[height\\]\\[value\\]]").select(
      "Moins de 3m",
      { force: true }
    );
    cy.get("#modal-filters button#modal-apply-button").click();
    cy.get("table tbody tr").should("have.length", 1);
    cy.get("table tbody tr").first().should("contain.text", "Stone");
  });

  it("show details", () => {
    cy.get("input[name=query]").type("Stone").type("{enter}");
    cy.get("tr a.dropdown-toggle").first().takeAction("Consulter");
    cy.get("h1").should("contain.text", "Stone");
    cy.get("#map")
      .should("have.class", "leaflet-container")
      .get("img.leaflet-marker-icon");
    cy.get(".drawing-container svg path")
      .invoke("attr", "d")
      .should("match", /^M474/);

    cy.contains("Moins de 3m");
  });
});

context("Boulders-write as superadmin", () => {
  beforeEach(() => {
    cy.task("loadDb");
    cy.realLogin("super-admin@fixture.com");
    cy.get("#main-menu").contains("Blocs").click();
  });

  it("updatedBy and updatedAt fields are automatically assigned", () => {
    cy.get("tr a.dropdown-toggle").first().takeAction("Consulter");
    cy.get(".cy_updated_at").contains("Mis à jour le");
    cy.get(".cy_updated_at").should("contain", "Aucun(e)");
    cy.get(".cy_updated_by").contains("Mis à jour par");
    cy.get(".cy_updated_by").should("contain", "Aucun(e)");
    cy.contains("Éditer").click();
    cy.get("input[name=Boulder\\[name\\]]").clear().type("Foo");
    cy.contains("Sauvegarder les modifications").click();
    cy.get("tr a.dropdown-toggle").first().takeAction("Consulter");
    cy.get(".cy_updated_at").should("not.contain", "Aucun(e)");
    cy.get(".cy_updated_by").should("contain", "super-admin@fixture.com");
  });
});

context("Boulders-write as admin", () => {
  beforeEach(() => {
    cy.task("loadDb");
    cy.realLogin("admin@fixture.com");
    cy.get("#main-menu").contains("Blocs").click();
  });

  it("delete a boulder", () => {
    cy.get("table tbody tr").first().deleteRow();
    cy.get("table tbody tr").should("have.length", 3);
  });

  it("create a boulder", () => {
    cy.contains("Créer Bloc").click();
    cy.get("h1").should("contain.text", 'Créer "Bloc"');
    cy.get("button.action-save").contains("Créer").click();
    cy.get("input[name=Boulder\\[name\\]]").should("have.class", "is-invalid");

    cy.get("input[name=Boulder\\[name\\]]").type("La route du rhum");
    cy.get("select[name=Boulder\\[rock\\]]").chooseOption("Menez Ham #3");

    cy.get("button.action-save").contains("Créer").click();
    cy.get("table tbody tr").should("have.length", 5);
  });

  it("cannot draw line if no picture associated to the rock", () => {
    cy.get("input[name=query]").type("L'essai").type("{enter}");
    cy.get("tr a.dropdown-toggle").first().takeAction("Consulter");
    cy.get("a").contains("Ligne du bloc").click();
    cy.get("h1").contains("Ligne du bloc");
    cy.get("p").contains(
      "Vous ne pouvez pas dessiner la ligne de bloc car aucune photo n'est associée au rocher."
    );
    cy.get("a").contains("Ajouter des photos").click();
    cy.get("h1").contains("Modifier Rocher");
  });

  it("can draw line", () => {
    cy.get("input[name=query]").type("Stone").type("{enter}");
    cy.get("tr a.dropdown-toggle").first().takeAction("Ligne du bloc");

    cy.get("img[data-cy=drawing-image]")
      .should("be.visible")
      .invoke("width")
      .should("eq", 550);
    cy.get("img[data-cy=drawing-image]").then((img) => {
      expect(Math.ceil(img.height())).to.eq(367);
    });

    cy.get(".drawing-container svg")
      .should("be.visible")
      .invoke("attr", "width")
      .should("eq", "550");

    cy.get(".drawing-container svg").then((svgEl) => {
      expect(Math.ceil(svgEl.height())).to.eq(367);
    });

    cy.intercept("DELETE", "/admin/line_boulders/*").as("deleteLineBoulder");
    cy.get("[aria-label=clear]").click();
    cy.get("[aria-label=save]").click();
    cy.wait("@deleteLineBoulder").its("response.statusCode").should("eq", 204);

    cy.get(".drawing-container svg")
      .realMouseDown()
      .realMouseMove(100, 100, { position: "center" });

    cy.intercept("POST", "/admin/line_boulders").as("postLineBoulder");
    cy.get("[aria-label=save]").click();
    cy.wait("@postLineBoulder").then((interception) => {
      const lineBoulderIri = interception.response.body["@id"];
      expect(interception.response.statusCode).to.eq(201);
      expect(interception.request.body).to.have.property("boulder");
      expect(interception.request.body).to.have.property("rockImage");
      expect(interception.request.body.arrArrPoints).to.have.length(1);
      expect(interception.request.body.arrArrPoints[0][0].x).to.be.a("number");
      expect(interception.request.body.arrArrPoints[0][0].y).to.be.a("number");

      cy.get(".drawing-container svg")
        .realMouseDown()
        .realMouseMove(50, 200, { position: "right" });

      cy.get(".drawing-container svg")
        .realMouseDown()
        .realMouseMove(250, 300, { position: "bottom" });

      cy.get("[aria-label=undo]").click();

      cy.intercept("PUT", lineBoulderIri).as("editLineBoulder");
      cy.get("[aria-label=save]").click();
      cy.wait("@editLineBoulder").then((interception) => {
        expect(interception.response.statusCode).to.eq(200);
        expect(interception.request.body).not.to.have.property("boulder");
        expect(interception.request.body).not.to.have.property("rockImage");
        expect(interception.request.body.arrArrPoints).to.have.length(2);
      });
    });
  });
});

context("Boulders-write as contributor", () => {
  beforeEach(() => {
    cy.task("loadDb");
    cy.realLogin();
    cy.get("#main-menu").contains("Blocs").click();
    cy.contains("Créer Bloc").click();
    cy.get("h1").should("contain.text", 'Créer "Bloc"');
    cy.get("button.action-save").contains("Créer").click();
    cy.get("input[name=Boulder\\[name\\]]").type("La route du rhum");
    cy.get("select[name=Boulder\\[rock\\]]").chooseOption("Cremiou #1");

    cy.get("button.action-save").contains("Créer").click();
    cy.get("table tbody tr").should("have.length", 5);
  });

  it("cannot delete or edit a boulder that does not belong to me", () => {
    cy.get("input[name=query]").type("L'essai").type("{enter}");
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

  it("i can delete a boulder created by me", () => {
    cy.get("input[name=query]").type("La route du rhum").type("{enter}");
    cy.get("table tbody tr").first().deleteRow();
    cy.get("input[name=query]").clear().type("{enter}");
    cy.get("table tbody tr").should("have.length", 4);
  });

  it("i can edit a boulder created by me", () => {
    cy.get("input[name=query]").type("La route du rhum").type("{enter}");
    cy.get("tr a.dropdown-toggle").first().takeAction("Éditer");
    cy.contains("Modifier Bloc");
  });

  it("can draw line for a boulder created by me", () => {
    cy.get("input[name=query]").type("La route du rhum").type("{enter}");
    cy.get("tr a.dropdown-toggle").first().takeAction("Ligne du bloc");

    cy.get(".drawing-container svg")
      .realMouseDown()
      .realMouseMove(100, 100, { position: "center" });

    cy.intercept("DELETE", "/admin/line_boulders/*").as("deleteLineBoulder");

    cy.intercept("POST", "/admin/line_boulders").as("postLineBoulder");
    cy.get("[aria-label=save]").click();
    cy.wait("@postLineBoulder").then((interception) => {
      const lineBoulderIri = interception.response.body["@id"];
      expect(interception.response.statusCode).to.eq(201);

      cy.intercept("PUT", lineBoulderIri).as("editLineBoulder");
      cy.get("[aria-label=save]").click();
      cy.wait("@editLineBoulder").then((interception) => {
        expect(interception.response.statusCode).to.eq(200);
      });

      cy.get("[aria-label=clear]").click();
      cy.get("[aria-label=save]").click();
      cy.wait("@deleteLineBoulder")
        .its("response.statusCode")
        .should("eq", 204);
    });
  });
});
