import express from "express";
import { execaCommandSync } from "execa";
const app = express();
const port = 80;

app.get("/dump", (_, res) => {
  execaCommandSync(`./dump-data.sh`);
  res.json({ success: true });
});

app.get("/load", (_, res) => {
  execaCommandSync(`./load-data.sh`);
  res.json({ success: true });
});

app.listen(port, () => {
  console.log(`listening on port ${port}`);
});
