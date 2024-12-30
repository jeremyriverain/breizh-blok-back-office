const { defineConfig } = require("cypress");
const { ofetch } = require("ofetch");
const runBaseUrl = "http://app_test";

function getMailCatcherUrl(baseUrl) {
  return baseUrl === runBaseUrl
    ? "http://mailcatcher:1080"
    : "http://localhost:1080";
}

const databaseBranchName = process.env.DATABASE_BRANCH_NAME ?? "main";

module.exports = defineConfig({
  e2e: {
    baseUrl: runBaseUrl,
    viewportHeight: 800,
    viewportWidth: 1280,
    experimentalInteractiveRunEvents: true,
    experimentalRunAllSpecs: true,
    specPattern: "cypress/integration/**/*.cy.js",
    requestTimeout: 10000,
    defaultCommandTimeout: 10000,
    setupNodeEvents(on, config) {
      on("before:run", async () => {
        return await ofetch(
          config.baseUrl + `/test/init-db/${databaseBranchName}`
        );
      });
      on("task", {
        async loadDb() {
          return await ofetch(
            config.baseUrl + `/test/load-db/${databaseBranchName}`
          );
        },
        async fetchEmails() {
          const mailcatcherUrl = getMailCatcherUrl(config.baseUrl);
          return await ofetch(`${mailcatcherUrl}/messages`);
        },
        async fetchEmail(id) {
          const mailcatcherUrl = getMailCatcherUrl(config.baseUrl);
          return await ofetch(`${mailcatcherUrl}/messages/${id}.plain`);
        },
      });
    },
  },
});
