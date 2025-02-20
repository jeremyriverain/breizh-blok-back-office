Cypress.Commands.add(
  "deleteRow",
  { prevSubject: "element" },
  (subject, options) => {
    cy.wrap(subject).within(() => {
      cy.get("a[data-action-name=delete]").click({ force: true });
    });
    cy.get("#modal-delete").should("be.visible").contains("Supprimer").click();
  }
);

Cypress.Commands.add(
  "takeAction",
  { prevSubject: "element" },
  (subject, action) => {
    cy.wrap(subject)
      .should("have.class", "dropdown-toggle")
      .click()
      .next()
      .find("a")
      .contains(action)
      .click();
  }
);

Cypress.Commands.add(
  "chooseOption",
  { prevSubject: "element" },
  (subject, label) => {
    expect(subject[0].nodeName).to.equal("SELECT");
    cy.wrap(subject)
      .next(".form-select")
      .click()
      .find(".plugin-dropdown_input [role=option]")
      .contains(label)
      .click({ force: true });
  }
);

Cypress.Commands.add("realLogin", (email = "contributor@fixture.com") => {
  cy.task("fetchEmails").as("beforeEmails");
  const body = new URLSearchParams();
  body.append("email", email);
  cy.request({
    method: "POST",
    url: "/admin/login/fr",
    failOnStatusCode: false,
    headers: {
      "content-type": "application/x-www-form-urlencoded",
    },
    body: Object.fromEntries(body),
    "content-type": "application/x-www-form-urlencoded",
  }).then((r) => {
    // cy.wait(200);
    cy.task("fetchEmails").then((emails) => {
      cy.get("@beforeEmails").then((beforeEmails) => {
        if (beforeEmails.length === emails.length) {
          cy.log("authentication failed");
          return;
        }
        cy.task("fetchEmail", emails[emails.length - 1].id).then((email) => {
          const loginUrl = email
            .match(/Se connecter: (.*)/g)[0]
            .replace("Se connecter: ", "");
          cy.visit(loginUrl, { failOnStatusCode: false });
        });
      });
    });
  });
});

Cypress.Commands.add("logout", () => {
  cy.request({ url: "/admin/logout", failOnStatusCode: false });
});

const originalVisit = cy.visit;

Cypress.Commands.overwrite("visit", (originalFn, url, options = {}) => {
  if (typeof url === "object") {
    options = url;
    url = options.url;
  }

  // Set the Accept-Language header
  options.headers = {
    "Accept-Language": "fr-FR",
    ...options.headers,
  };

  return originalFn(url, options);
});
