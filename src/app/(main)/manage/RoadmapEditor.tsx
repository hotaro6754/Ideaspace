"use client";

import { useState } from "react";
import { Plus, Check, Trash2 } from "lucide-react";
import { Button } from "@/components/ui/Button";

export const RoadmapEditor = ({ milestones, onUpdate }: { milestones: any[], onUpdate: (m: any[]) => void }) => {
  const [items, setItems] = useState(milestones || []);
  const [newLabel, setNewLabel] = useState("");

  const addItem = () => {
    if (!newLabel) return;
    const newItems = [...items, { label: newLabel, status: 'pending', date: new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) }];
    setItems(newItems);
    setNewLabel("");
    onUpdate(newItems);
  };

  const toggleStatus = (index: number) => {
    const newItems = [...items];
    newItems[index].status = newItems[index].status === 'completed' ? 'pending' : 'completed';
    setItems(newItems);
    onUpdate(newItems);
  };

  const removeItem = (index: number) => {
    const newItems = items.filter((_, i) => i !== index);
    setItems(newItems);
    onUpdate(newItems);
  };

  return (
    <div className="space-y-6">
      <div className="flex gap-4">
        <input
          type="text"
          placeholder="New milestone..."
          value={newLabel}
          onChange={(e) => setNewLabel(e.target.value)}
          className="flex-1 bg-white/5 border border-white/5 rounded-xl px-4 text-xs font-bold focus:outline-none focus:border-lendi-blue/50"
        />
        <Button onClick={addItem} className="rounded-xl px-4 h-12">Add +</Button>
      </div>

      <div className="space-y-3">
        {items.map((m, i) => (
          <div key={i} className="flex items-center justify-between p-4 rounded-2xl bg-white/5 border border-white/5 group">
            <div className="flex items-center gap-4">
              <button onClick={() => toggleStatus(i)} className={`p-1.5 rounded-md transition-colors ${m.status === 'completed' ? "bg-green-500/20 text-green-500" : "bg-white/10 text-white/20"}`}>
                <Check className="w-3.5 h-3.5" />
              </button>
              <span className={`text-xs font-bold ${m.status === 'completed' ? "text-white/40 line-through" : "text-white"}`}>{m.label}</span>
            </div>
            <div className="flex items-center gap-4">
              <span className="text-[9px] font-black uppercase text-white/10">{m.date}</span>
              <button onClick={() => removeItem(i)} className="text-white/0 group-hover:text-red-500 transition-colors">
                <Trash2 className="w-3.5 h-3.5" />
              </button>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
};
