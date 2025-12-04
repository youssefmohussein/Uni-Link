export default function Stats() {
  return (
    <div className="flex flex-wrap gap-4 md:gap-8 mb-10 font-main transition-smooth">

      <div>
        <p className="text-2xl font-semibold text-main font-secondary">
          1,250
        </p>
        <p className="text-muted">Points</p>
      </div>

      <div>
        <p className="text-2xl font-semibold text-main font-secondary">
          Advanced
        </p>
        <p className="text-muted">Level</p>
      </div>

      <div>
        <p className="text-2xl font-semibold text-main font-secondary">
          15
        </p>
        <p className="text-muted">Projects</p>
      </div>

    </div>
  );
}
