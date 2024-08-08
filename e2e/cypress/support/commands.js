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

Cypress.Commands.add("realLogin", (email = "user@fixture.com") => {
  cy.task("fetchEmails").as("beforeEmails");
  cy.visit("/admin/login");
  cy.get("input[name=email]").clear().type(email);
  cy.contains("Envoyer lien").click();
  // cy.wait(100)
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
        cy.visit(loginUrl);
      });
    });
  });
});

Cypress.Commands.add("logout", () => {
  cy.request("/admin/logout");
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
