const { defineConfig } = require("cypress");
const { ofetch } = require("ofetch");

const runBaseUrl = "http://app_test";

function getMailCatcherUrl(baseUrl) {
  return baseUrl === runBaseUrl
    ? "http://mailcatcher:1080"
    : "http://localhost:1080";
}

module.exports = defineConfig({
  e2e: {
    baseUrl: runBaseUrl,
    viewportHeight: 800,
    viewportWidth: 1280,
    experimentalInteractiveRunEvents: true,
    experimentalRunAllSpecs: true,
    specPattern: "cypress/integration/**/*.cy.js",
    setupNodeEvents(on, config) {
      on("before:run", async () => {
        await ofetch(config.baseUrl + "/test/init-db");
        return await ofetch(config.baseUrl + "/test/dump-db");
      });
      on("task", {
        async loadDb() {
          return await ofetch(config.baseUrl + "/test/load-db");
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
