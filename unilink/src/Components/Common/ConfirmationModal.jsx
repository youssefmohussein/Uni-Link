import React from 'react';

const ConfirmationModal = ({
    isOpen,
    onClose,
    onConfirm,
    title,
    message,
    confirmText = 'Delete',
    cancelText = 'Cancel',
    isDanger = true
}) => {
    if (!isOpen) return null;

    return (
        <>
            <div className="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/50 ">
                <div
                   
                   
                   
                    className="bg-[#1c2128] border border-[#30363d] rounded-xl shadow-2xl max-w-md w-full overflow-hidden"
                >
                    <div className="p-6">
                        <h3 className="text-xl font-bold text-white mb-2">{title}</h3>
                        <p className="text-gray-400 mb-6">{message}</p>

                        <div className="flex justify-end gap-3">
                            <button
                                onClick={onClose}
                                className="px-4 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-[#30363d] transition-colors font-medium border border-[#30363d]"
                            >
                                {cancelText}
                            </button>
                            <button
                                onClick={() => { onConfirm(); onClose(); }}
                                className={`px-4 py-2 rounded-lg text-white font-medium transition-colors ${isDanger
                                        ? 'bg-red-500 hover:bg-red-600 shadow-[0_4px_10px_rgba(239,68,68,0.3)]'
                                        : 'bg-[#58a6ff] hover:bg-[#3b82f6] shadow-[0_4px_10px_rgba(88,166,255,0.3)]'
                                    }`}
                            >
                                {confirmText}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
};

export default ConfirmationModal;
