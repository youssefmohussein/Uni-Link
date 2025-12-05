import { API_BASE_URL } from "../config/api";

export async function apiRequest(endpoint, method = "GET", data = null) {
  const url = `${API_BASE_URL}/${endpoint}`;
  console.log("Fetching from:", url);

  const options = {
    method,
    headers: { "Content-Type": "application/json" },
    credentials: "include", // Important for session-based authentication
  };

  if (data) {
    options.body = JSON.stringify(data);
  }

  try {
    const response = await fetch(url, options);
    if (!response.ok) {
      throw new Error(`API error: ${response.status}`);
    }
    const json = await response.json();
    console.log("Response:", json);
    return json;
  } catch (err) {
    console.error("❌ API Request failed:", err);
    throw err;
  }
}
