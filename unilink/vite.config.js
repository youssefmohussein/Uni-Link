import { defineConfig, loadEnv } from 'vite'
import react from '@vitejs/plugin-react'
import tailwindcss from '@tailwindcss/vite'

// https://vite.dev/config/
export default defineConfig(({ mode }) => {
  // Load env vars for the current mode (.env.local, .env, etc.)
  const env = loadEnv(mode, process.cwd(), '')

  // The PHP backend URL — used for the dev proxy fallback
  const backendUrl = env.VITE_API_BASE_URL || 'http://localhost:8000'

  return {
    plugins: [react(), tailwindcss()],

    server: {
      /**
       * Dev proxy — forwards API calls to the PHP backend.
       * This means in development you can also call fetch('/login')
       * without needing the full URL, but we keep VITE_API_BASE_URL
       * as the primary mechanism so production deploys just work.
       */
      proxy: {
        // ── /api/* catch-all (covers all modern routes) ──────────────
        '/api': { target: backendUrl, changeOrigin: true },

        // ── Legacy routes (no /api prefix) ────────────────────────────
        // Auth
        '/login':          { target: backendUrl, changeOrigin: true },
        '/logout':         { target: backendUrl, changeOrigin: true },
        '/check-session':  { target: backendUrl, changeOrigin: true },
        '/health':         { target: backendUrl, changeOrigin: true },

        // Users / Students / Professors / Admin
        '/getUsers':           { target: backendUrl, changeOrigin: true },
        '/getUserProfile':     { target: backendUrl, changeOrigin: true },
        '/getStudents':        { target: backendUrl, changeOrigin: true },
        '/getProfessorById':   { target: backendUrl, changeOrigin: true },
        '/getDashboardStats':  { target: backendUrl, changeOrigin: true },
        '/getAllAdmins':        { target: backendUrl, changeOrigin: true },
        '/updateAdmin':        { target: backendUrl, changeOrigin: true },

        // Posts
        '/getAllPosts':    { target: backendUrl, changeOrigin: true },
        '/addPost':        { target: backendUrl, changeOrigin: true },
        '/createPost':     { target: backendUrl, changeOrigin: true },
        '/updatePost':     { target: backendUrl, changeOrigin: true },
        '/deletePost':     { target: backendUrl, changeOrigin: true },
        '/searchPosts':    { target: backendUrl, changeOrigin: true },
        '/getUserPosts':   { target: backendUrl, changeOrigin: true },
        '/uploadMedia':    { target: backendUrl, changeOrigin: true },
        '/uploadPostMedia':{ target: backendUrl, changeOrigin: true },
        '/getCategoryCounts': { target: backendUrl, changeOrigin: true },

        // Post interactions / comments
        '/addInteraction':       { target: backendUrl, changeOrigin: true },
        '/getInteractionsByPost':{ target: backendUrl, changeOrigin: true },
        '/getUserReaction':      { target: backendUrl, changeOrigin: true },
        '/getReactionCounts':    { target: backendUrl, changeOrigin: true },
        '/deleteInteraction':    { target: backendUrl, changeOrigin: true },
        '/addComment':           { target: backendUrl, changeOrigin: true },
        '/getComments':          { target: backendUrl, changeOrigin: true },
        '/updateComment':        { target: backendUrl, changeOrigin: true },
        '/deleteComment':        { target: backendUrl, changeOrigin: true },

        // Projects
        '/uploadProject':   { target: backendUrl, changeOrigin: true },
        '/getUserProjects': { target: backendUrl, changeOrigin: true },
        '/deleteProject':   { target: backendUrl, changeOrigin: true },
        '/updateProject':   { target: backendUrl, changeOrigin: true },

        // Rooms / Chat
        '/getAllRooms':    { target: backendUrl, changeOrigin: true },
        '/getUserRooms':   { target: backendUrl, changeOrigin: true },
        '/createRoom':     { target: backendUrl, changeOrigin: true },
        '/getRoom':        { target: backendUrl, changeOrigin: true },
        '/updateRoom':     { target: backendUrl, changeOrigin: true },
        '/deleteRoom':     { target: backendUrl, changeOrigin: true },
        '/joinRoom':       { target: backendUrl, changeOrigin: true },
        '/sendMessage':    { target: backendUrl, changeOrigin: true },
        '/getMessages':    { target: backendUrl, changeOrigin: true },
        '/deleteMessage':  { target: backendUrl, changeOrigin: true },
        '/getRoomMembers': { target: backendUrl, changeOrigin: true },
        '/getRoomCount':   { target: backendUrl, changeOrigin: true },
        '/uploadChatFile': { target: backendUrl, changeOrigin: true },

        // CV
        '/getCV':      { target: backendUrl, changeOrigin: true },
        '/downloadCV': { target: backendUrl, changeOrigin: true },
        '/deleteCV':   { target: backendUrl, changeOrigin: true },
        '/uploadCV':   { target: backendUrl, changeOrigin: true },

        // Skills / Faculties / Majors
        '/addSkillCategory':  { target: backendUrl, changeOrigin: true },
        '/addSkill':          { target: backendUrl, changeOrigin: true },
        '/addUserSkills':     { target: backendUrl, changeOrigin: true },
        '/removeUserSkill':   { target: backendUrl, changeOrigin: true },
        '/getAllFaculties':   { target: backendUrl, changeOrigin: true },
        '/getFaculty':       { target: backendUrl, changeOrigin: true },
        '/seedFaculties':    { target: backendUrl, changeOrigin: true },
        '/getAllMajors':      { target: backendUrl, changeOrigin: true },
        '/addFaculty':       { target: backendUrl, changeOrigin: true },
        '/updateFaculty':    { target: backendUrl, changeOrigin: true },
        '/deleteFaculty':    { target: backendUrl, changeOrigin: true },
        '/addMajor':         { target: backendUrl, changeOrigin: true },
        '/updateMajor':      { target: backendUrl, changeOrigin: true },
        '/deleteMajor':      { target: backendUrl, changeOrigin: true },

        // Static uploads served by PHP
        '/uploads': { target: backendUrl, changeOrigin: true },
      },
    },
  }
})

