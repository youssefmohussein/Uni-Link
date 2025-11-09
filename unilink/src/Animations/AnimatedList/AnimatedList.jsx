// src/Animations/AnimatedList/AnimatedList.jsx
import { motion, useInView } from "framer-motion";
import { useRef } from "react";

export default function AnimatedList({
  items = [],
  renderItem,
  stagger = 0.05,
  once = false, // true = animate once, false = animate every time on scroll
}) {
  return (
    <div className="divide-y divide-white/10">
      {items.map((item, index) => (
        <AnimatedRow
          key={item.user_id || index}
          index={index}
          stagger={stagger}
          once={once}
        >
          {renderItem(item, index)}
        </AnimatedRow>
      ))}
    </div>
  );
}

function AnimatedRow({ children, index, stagger, once }) {
  const ref = useRef(null);

  // 👇 useInView on each row, not the whole list
  const inView = useInView(ref, {
    margin: "-10% 0px -10% 0px", // triggers slightly before/after visible
    amount: 0.3, // 30% of the row must be visible
    once,        // false = animate every time you scroll past
  });

  return (
    <motion.div
      ref={ref}
      initial={{ opacity: 0, y: 40 }}
      animate={inView ? { opacity: 1, y: 0 } : { opacity: 0, y: 40 }}
      transition={{
        duration: 0.4,
        delay: index * stagger,
        ease: "easeOut",
      }}
      className="will-change-transform"
    >
      {children}
    </motion.div>
  );
}
