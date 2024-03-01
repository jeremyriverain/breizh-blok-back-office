import http from "k6/http";

export const options = {
  vus: 5,
  duration: "30s",
};

export default function () {
  const headers = { Accept: "application/json" };
  // requête permettant de récupérer 1500 marqueurs de position.
  http.get(
    "https://boulders.geekco.fr/boulders?page=1&itemsPerPage=1500&groups%5B%5D=Boulder%3Amap",
    null,
    { headers }
  );
}
