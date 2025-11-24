// // src/Components/Admin_Components/ActivityTimeline.jsx
// import React from "react";
// import { motion } from "framer-motion";

// export default function ActivityTimeline({ activities }) {
//   return (
//     <div className="bg-panel rounded-custom p-5 border border-border shadow-md max-h-96 overflow-y-auto">
//       <h3 className="text-lg font-semibold mb-4">Recent Activity</h3>
//       <ul>
//         {activities.map((act, i) => (
//           <motion.li
//             key={i}
//             className="p-2 border-b border-border last:border-none hover:bg-hover-bg rounded transition-smooth"
//             initial={{ opacity: 0, x: -10 }}
//             animate={{ opacity: 1, x: 0 }}
//             transition={{ delay: i * 0.05 }}
//           >
//             <p className="text-muted text-sm">{act.time}</p>
//             <p>{act.description}</p>
//           </motion.li>
//         ))}
//       </ul>
//     </div>
//   );
// }
