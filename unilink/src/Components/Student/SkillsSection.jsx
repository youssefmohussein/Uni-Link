import { useState, useEffect } from "react";
import * as studentHandler from "../../../api/studentHandler";

export default function SkillsSection({ userId }) {
  const [skills, setSkills] = useState({});
  const [userCategories, setUserCategories] = useState([]); // Categories user has skills in
  const [allCategories, setAllCategories] = useState([]); // All available system categories
  const [allSkills, setAllSkills] = useState([]); // All available system skills
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [selectedSkillId, setSelectedSkillId] = useState("");
  const [selectedCategoryId, setSelectedCategoryId] = useState("");
  const [saving, setSaving] = useState(false);

  useEffect(() => {
    if (userId) {
      loadData();
    }
  }, [userId]);

  const loadData = async () => {
    try {
      setLoading(true);
      await Promise.all([fetchUserSkills(), fetchSystemCategories(), fetchSystemSkills()]);
    } catch (err) {
      console.error("Failed to load skills data:", err);
    } finally {
      setLoading(false);
    }
  };

  const fetchSystemCategories = async () => {
    try {
      const cats = await studentHandler.getSkillCategories();
      setAllCategories(cats || []);
    } catch (err) {
      console.error("Failed to fetch categories:", err);
    }
  };

  const fetchSystemSkills = async () => {
    try {
      const skillsData = await studentHandler.getAllSkills();
      setAllSkills(skillsData || []);
    } catch (err) {
      console.error("Failed to fetch all skills:", err);
    }
  };

  const fetchUserSkills = async () => {
    try {
      const userSkills = await studentHandler.getStudentSkills(userId);

      // Group skills by category
      const groupedSkills = {};
      const categorySet = new Set();

      userSkills.forEach(skill => {
        const categoryName = skill.category_name || "Other";
        categorySet.add(categoryName);

        if (!groupedSkills[categoryName]) {
          groupedSkills[categoryName] = [];
        }
        groupedSkills[categoryName].push({
          name: skill.skill_name,
          id: skill.skill_id
        });
      });

      setSkills(groupedSkills);
      setUserCategories(Array.from(categorySet));
    } catch (err) {
      console.error("Failed to fetch skills:", err);
      setSkills({});
      setUserCategories([]);
    }
  };

  const handleAddSkill = async () => {
    if (!selectedCategoryId || !selectedSkillId) {
      alert("Please select a category and a skill");
      return;
    }

    try {
      setSaving(true);

      // Use existing skill ID directly
      // Backend expects { skill_id: 123 }
      await studentHandler.addStudentSkills(userId, [{ skill_id: Number(selectedSkillId) }]);

      setSelectedSkillId("");

      // Refresh skills
      await fetchUserSkills();
    } catch (err) {
      console.error("Failed to add skill:", err);
      alert(err.message || "Failed to add skill. Please try again.");
    } finally {
      setSaving(false);
    }
  };

  const handleRemoveSkill = async (categoryName, skillId, skillName) => {
    if (!confirm(`Remove "${skillName}" from your skills?`)) {
      return;
    }

    try {
      // Use studentHandler to remove skill
      await studentHandler.removeStudentSkill(userId, skillId);

      // Update local state by re-fetching to be safe and consistent
      await fetchUserSkills();

    } catch (err) {
      console.error("Failed to remove skill:", err);
      alert(err.message || "Failed to remove skill. Please try again.");
    }
  };

  // Filter skills based on selected category AND exclude skills user already has
  const existingSkillIds = new Set(Object.values(skills).flat().map(s => s.id));

  const filteredSkills = allCategories.length > 0 && selectedCategoryId
    ? allSkills.filter(s =>
      s.category_id === Number(selectedCategoryId) &&
      !existingSkillIds.has(s.skill_id)
    )
    : [];

  return (
    <section className="rounded-xl border border-white/10 bg-white/5 backdrop-blur-sm p-6 shadow-lg"
      style={{ backdropFilter: 'blur(20px) saturate(180%)', WebkitBackdropFilter: 'blur(20px) saturate(180%)' }}>
      <div className="flex justify-between items-center mb-4">
        <h2 className="text-lg font-semibold text-white">💻 Skills</h2>
        <button
          onClick={() => setShowForm(!showForm)}
          className="text-sm px-3 py-1.5 rounded-lg bg-accent text-white hover:bg-accent/90 transition"
        >
          {showForm ? "Close" : "+ Add Skill"}
        </button>
      </div>

      {showForm && (
        <div className="bg-white/10 rounded-lg p-4 mb-5 space-y-3 border border-white/10">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
            {/* Category Dropdown */}
            <select
              value={selectedCategoryId}
              onChange={(e) => {
                setSelectedCategoryId(e.target.value);
                setSelectedSkillId(""); // Reset skill when category changes
              }}
              className="w-full rounded-lg bg-transparent border border-white/20 text-white px-3 py-2 text-sm focus:ring-2 focus:ring-accent focus:outline-none"
            >
              <option value="" className="text-black">Select Category</option>
              {allCategories.map((cat) => (
                <option key={cat.category_id} value={cat.category_id} className="text-black">
                  {cat.name}
                </option>
              ))}
            </select>

            {/* Skill Dropdown */}
            <select
              value={selectedSkillId}
              onChange={(e) => setSelectedSkillId(e.target.value)}
              disabled={!selectedCategoryId}
              className="w-full rounded-lg bg-transparent border border-white/20 text-white px-3 py-2 text-sm focus:ring-2 focus:ring-accent focus:outline-none disabled:opacity-50"
            >
              <option value="" className="text-black">
                {selectedCategoryId ? "Select Skill" : "Select Category First"}
              </option>
              {filteredSkills.map((skill) => (
                <option key={skill.skill_id} value={skill.skill_id} className="text-black">
                  {skill.name}
                </option>
              ))}
            </select>
          </div>

          <div className="flex justify-between items-center text-xs text-gray-400 px-1">
            <span>{selectedCategoryId ? `${filteredSkills.length} skills available to add` : ''}</span>
            {allSkills.length === 0 && !loading && <span className="text-red-400">Error: No skills loaded from DB</span>}
          </div>

          <div className="flex gap-2 justify-end">
            <button
              onClick={handleAddSkill}
              disabled={saving || !selectedCategoryId || !selectedSkillId}
              className="px-4 py-1.5 text-sm bg-accent text-white rounded-lg hover:bg-accent/90 transition disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {saving ? "Adding..." : "Add Skill"}
            </button>
          </div>
        </div>
      )}

      {loading ? (
        <div className="text-center py-8">
          <div className="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-accent"></div>
          <p className="mt-2 text-sm text-muted">Loading skills...</p>
        </div>
      ) : userCategories.length === 0 ? (
        <div className="text-center py-8">
          <p className="text-muted">No skills added yet. Add your first skill!</p>
        </div>
      ) : (
        <div className="space-y-5">
          {userCategories.map((category) => (
            <div key={category}>
              <h3 className="text-base font-semibold text-accent mb-2">
                {category}
              </h3>
              <div className="flex flex-wrap gap-2">
                {skills[category]?.map((skill, index) => (
                  <span
                    key={`${skill.id || skill.name}-${index}`}
                    className="group px-2.5 py-1 text-sm rounded-md bg-white/10 border border-white/10 text-gray-200 hover:bg-white/20 transition flex items-center gap-1"
                  >
                    {typeof skill === 'string' ? skill : skill.name}
                    <button
                      onClick={() => handleRemoveSkill(category, skill.id, skill.name)}
                      className="ml-1 text-red-400 hover:text-red-300 opacity-0 group-hover:opacity-100 transition-opacity"
                      title="Remove skill"
                    >
                      ×
                    </button>
                  </span>
                ))}
              </div>
            </div>
          ))}
        </div>
      )}
    </section>
  );
}
