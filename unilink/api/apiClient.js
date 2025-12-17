import { API_BASE_URL } from "../config/api";

export async function apiRequest(endpoint, method = "GET", data = null) {
  // Remove leading slash from endpoint if present, then add it back
  const cleanEndpoint = endpoint.startsWith('/') ? endpoint.substring(1) : endpoint;
  const url = `${API_BASE_URL}/${cleanEndpoint}`;
  console.log("Fetching from:", url);

  const options = {
    method,
    headers: { "Content-Type": "application/json" },
    credentials: "include", // Important for session-based authentication
  };

  if (data instanceof FormData) {
    delete options.headers["Content-Type"];
    options.body = data;
  } else if (data) {
    options.body = JSON.stringify(data);
  }

  try {
    const response = await fetch(url, options);

    // Get response text first
    const responseText = await response.text();

    // Try to parse as JSON
    let json;
    try {
      json = JSON.parse(responseText);
    } catch (parseError) {
      // If not JSON, it's likely an HTML error page
      console.error("❌ Backend returned HTML instead of JSON:", responseText.substring(0, 500));

      // Try to extract error message from HTML
      const errorMatch = responseText.match(/<b>(.+?)<\/b>/);
      const errorMessage = errorMatch ? errorMatch[1] : "Backend error - check console for details";

      throw new Error(`Backend Error: ${errorMessage}`);
    }

    if (!response.ok) {
      throw new Error(`API error: ${response.status} - ${json.message || 'Unknown error'}`);
    }

    console.log("Response:", json);
    return json;
  } catch (err) {
    console.error("❌ API Request failed:", err);
    throw err;
  }
}
