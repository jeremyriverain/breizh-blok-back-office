import http from "k6/http";

export const options = {
  vus: 5,
  duration: "30s",
};

export default function () {
  const headers = { Accept: "application/json" };
  http.get(
    "https://boulders.geekco.fr/boulders?page=1&pagination=false&groups%5B%5D=Boulder%3Amap",
    null,
    { headers }
  );
}
