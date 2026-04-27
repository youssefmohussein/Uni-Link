// // src/Components/Admin_Components/ActivityTimeline.jsx
// import React from "react";
// 
// export default function ActivityTimeline({ activities }) {
//   return (
//     <div className="bg-panel rounded-custom p-5 border border-border shadow-md max-h-96 overflow-y-auto">
//       <h3 className="text-lg font-semibold mb-4">Recent Activity</h3>
//       <ul>
//         {activities.map((act, i) => (
//           <li
//             key={i}
//             className="p-2 border-b border-border last:border-none hover:bg-hover-bg rounded transition-smooth"
//            
//            
//            
//           >
//             <p className="text-muted text-sm">{act.time}</p>
//             <p>{act.description}</p>
//           </li>
//         ))}
//       </ul>
//     </div>
//   );
// }
