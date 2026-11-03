import { useLocalStorage, useMediaQuery } from "usehooks-ts";
import { Toaster } from "react-hot-toast";

import { AvatarBackgroundModal } from "./components/avatar/AvatarBackgroundModal";
import { AvatarBackgroundPicker } from "./components/avatar/AvatarBackgroundPicker";
import { AvatarCanvas } from "./components/avatar/AvatarCanvas";
import { AvatarPartModal } from "./components/avatar/AvatarPartModal";
import { AvatarPartPicker } from "./components/avatar/AvatarPartPicker";
import { AvatarTooltip } from "./components/avatar/AvatarTooltip";
import { AvatarDownloadOptionModal } from "./components/avatar/AvatarDownloadOptionModal";
import { backgrounds } from "./constants/backgrounds";
import { Footer } from "./components/Footer";

import { useAvatar } from "./hooks/useAvatar";
import { useSounds } from "./hooks/useSounds";
import { Selector } from "./components/parts/Selector";
import { FAQs } from "./components/FAQs";
import { useEffect } from "react";
import { Confetti } from "@neoconfetti/react";
import html2canvas from "html2canvas";

const Title = () => <h1 className="font-bold text-3xl">Avatartion</h1>;

function App() {
  const isEmbedded = window.parent !== window;
  const [soundEnabled, setSoundEnabled] = useLocalStorage("soundEnabled", true);
  const {
    avatar,
    avatarPartsPickers,
    restAvatarPartsPickers,
    isAvatarModalPickerOpen,
    isBackgroundModalOpen,
    isShared,
    avatarModal,
    avatarCanvasRef,
    isDownloadOptionModalOpen,
    showConfetti,
    confettiOptions,
    setAvatar,
    setIsAvatarModalPickerOpen,
    setIsBackgroundModalOpen,
    setIsDownloadOptionModalOpen,
    openAvatarModalPicker,
    closeAvatarModalPicker,
    openAvatarBackgroundModal,
    openAvatarDownloadOptionModal,
    handleDownloadAvatarPNG,
    handleDownloadAvatarSVG,
    handleRandomizeAvatar,
    share,
    serialize,
  } = useAvatar({ soundEnabled });

  useEffect(() => {
    if (!isShared) return;

    const currentParams = new URLSearchParams(window.location.search);
    if (currentParams.has("shared")) {
      currentParams.delete("shared");
    }

    currentParams.set("avatar", serialize());
    window.history.pushState(null, "", `?${currentParams.toString()}`);
  }, [avatar, isShared]);

  const { playPauseSound } = useSounds({ soundEnabled });

  const isMobile = useMediaQuery("(max-width: 768px)");

  const toggleSound = (enabled: boolean) => {
    setSoundEnabled(enabled);
    if (enabled) {
      playPauseSound();
    }
  };

  const useAvatarInProfile = async () => {
    if (!avatarCanvasRef.current || window.parent === window) {
      return;
    }

    try {
      const canvas = await html2canvas(avatarCanvasRef.current, {
        backgroundColor: null,
        scale: 2,
      });

      window.parent.postMessage(
        {
          type: "avatar-ready",
          dataUrl: canvas.toDataURL("image/png"),
        },
        window.location.origin,
      );
    } catch (error) {
      console.error("Could not export avatar", error);
    }
  };

  if (Object.entries(avatar).length === 0) return null;

  if (isEmbedded) {
    return (
      <>
        <div className="flex min-h-[430px] w-full items-center justify-center overflow-x-auto bg-white px-4 py-4 text-center">
          <div className="grid min-w-[760px] grid-cols-[110px_294px_280px] items-center justify-items-center gap-8">
            <div className="flex flex-col items-center gap-3">
              <AvatarPartPicker
                path="base/Download"
                onClick={() => openAvatarDownloadOptionModal()}
              />
              <AvatarPartPicker
                path="base/Reload"
                onClick={() => handleRandomizeAvatar()}
              />
              <AvatarPartPicker path="base/Share" onClick={() => share()} />
              <AvatarPartPicker
                path={`base/${soundEnabled ? "SoundLoud" : "SoundOff"}`}
                onClick={() => toggleSound(!soundEnabled)}
              />
              <button
                type="button"
                onClick={useAvatarInProfile}
                className="rounded-full bg-teal-500 px-3 py-2 text-xs font-semibold text-white shadow transition hover:bg-teal-600"
              >
                Dùng avatar
              </button>
            </div>

            <div className="flex h-[294px] w-[294px] items-center justify-center">
              <AvatarCanvas {...avatar} ref={avatarCanvasRef} />
            </div>

            <div className="grid grid-cols-2 items-center gap-4">
              {[...avatarPartsPickers, ...restAvatarPartsPickers].map((picker) => (
                <div key={picker.path} className="flex items-center justify-center">
                  {picker.part !== "bg" ? (
                    <AvatarPartPicker
                      path={picker.path}
                      onClick={() => openAvatarModalPicker(picker)}
                    />
                  ) : (
                    <AvatarBackgroundPicker
                      color={avatar.bg}
                      onClick={() => openAvatarBackgroundModal()}
                    />
                  )}
                </div>
              ))}
            </div>
          </div>
        </div>
        <AvatarPartModal
          {...avatarModal}
          isOpen={isAvatarModalPickerOpen}
          onPartSelected={(part, src) => closeAvatarModalPicker(part, src)}
          onClose={() => setIsAvatarModalPickerOpen(false)}
        />
        <AvatarBackgroundModal
          isOpen={isBackgroundModalOpen}
          backgrounds={backgrounds}
          activeBackground={avatar.bg}
          onBackgroundSelected={(bg) => setAvatar((prev) => ({ ...prev, bg }))}
          onClose={() => setIsBackgroundModalOpen(false)}
        />
        <AvatarDownloadOptionModal
          isOpen={isDownloadOptionModalOpen}
          onDownloadOption={(option: "SVG" | "PNG") =>
            option === "SVG" ? handleDownloadAvatarSVG() : handleDownloadAvatarPNG()
          }
          onClose={() => setIsDownloadOptionModalOpen(false)}
        />
      </>
    );
  }

  return (
    <>
      <div className="overflow-hidden">
        {!isMobile ? (
          <div className="mx-auto w-[750px] text-center h-auto">
            {
              <div className="flex items-center justify-center pt-5 md:pt-[5vh] mb-4 md:mb-0">
                <Title />
              </div>
            }
            <div className="flex items-center justify-center relative">
            {showConfetti && <Confetti {...confettiOptions} />}
              <div className="flex h-[44vh] items-center justify-center md:h-[47vh]">
                <AvatarCanvas {...avatar} ref={avatarCanvasRef} />
              </div>
              <div className="absolute -right-6">
                <div className="flex space-x-6">
                  <div className="flex flex-col space-y-4">
                    {avatarPartsPickers.map((picker) => (
                      <AvatarTooltip
                        key={picker.path}
                        text={picker.text}
                        width={picker.width}
                      >
                        <div className="flex items-center">
                          <AvatarPartPicker
                            path={picker.path}
                            onClick={() => openAvatarModalPicker(picker)}
                          />
                          {picker.isModal && (
                            <Selector
                              onSelectorClick={() =>
                                openAvatarModalPicker(picker)
                              }
                            />
                          )}
                        </div>
                      </AvatarTooltip>
                    ))}
                  </div>
                  <div className="flex flex-col space-y-4">
                    {restAvatarPartsPickers.map((picker) => (
                      <AvatarTooltip
                        key={picker.path}
                        text={picker.text}
                        width={picker.width}
                      >
                        <div className="flex items-center">
                          {picker.part !== "bg" ? (
                            <AvatarPartPicker
                              path={picker.path}
                              onClick={() => openAvatarModalPicker(picker)}
                            />
                          ) : (
                            <AvatarBackgroundPicker
                              color={avatar.bg}
                              onClick={() => openAvatarBackgroundModal()}
                            />
                          )}
                          {picker.isModal && (
                            <Selector
                              onSelectorClick={() =>
                                picker.part !== "bg"
                                  ? openAvatarModalPicker(picker)
                                  : openAvatarBackgroundModal()
                              }
                            />
                          )}
                        </div>
                      </AvatarTooltip>
                    ))}
                  </div>
                </div>
              </div>
              <div className="absolute left-24">
                <div className="flex flex-col space-y-2 md:space-y-4">
                  <AvatarTooltip text="Download" width={60}>
                    <AvatarPartPicker
                      path="base/Download"
                      onClick={() => openAvatarDownloadOptionModal()}
                    />
                  </AvatarTooltip>
                  <AvatarTooltip text="Randomize" width={60}>
                    <AvatarPartPicker
                      path="base/Reload"
                      onClick={() => handleRandomizeAvatar()}
                    />
                  </AvatarTooltip>
                  <AvatarTooltip text="Share" width={60}>
                    <AvatarPartPicker
                      path="base/Share"
                      onClick={() => share()}
                    />
                  </AvatarTooltip>
                  <AvatarTooltip text="Sound" width={60}>
                    <AvatarPartPicker
                      path={`base/${soundEnabled ? "SoundLoud" : "SoundOff"}`}
                      onClick={() => toggleSound(!soundEnabled)}
                    />
                  </AvatarTooltip>
                  <button
                    type="button"
                    onClick={useAvatarInProfile}
                    className="rounded-full bg-teal-500 px-3 py-2 text-xs font-semibold text-white shadow transition hover:bg-teal-600"
                  >
                    Dùng avatar
                  </button>
                </div>
              </div>
            </div>
            <div className="flex flex-col items-center justify-center px-4 space-y-2 pb-12">
              <FAQs />
              <Footer />
            </div>
          </div>
        ) : (
          <div className="mx-auto text-center sm:w-3/4 md:w-1/2">
            <div className="flex items-center justify-center pt-5 md:pt-[5vh] mb-4 md:mb-0">
              <Title />
            </div>
            <div className="flex items-center justify-center h-[44vh] md:h-[47vh]">
              <AvatarCanvas {...avatar} ref={avatarCanvasRef} />
            </div>
            <div className="flex items-center justify-center">
            {showConfetti && <Confetti {...confettiOptions} />}
              <div className="flex flex-col items-center justify-center px-4 pt-6 space-y-2 overflow-x-auto">
                <div className="flex space-x-3 md:space-x-4 ">
                  {avatarPartsPickers.map((picker) => (
                    <AvatarTooltip
                      key={picker.path}
                      text={picker.text}
                      width={picker.width}
                    >
                      <div className="flex items-center overflow-x-auto">
                        {picker.part !== "bg" ? (
                          <AvatarPartPicker
                            path={picker.path}
                            onClick={() => openAvatarModalPicker(picker)}
                          />
                        ) : (
                          <AvatarBackgroundPicker
                            color={avatar.bg}
                            onClick={() => openAvatarBackgroundModal()}
                          />
                        )}
                      </div>
                    </AvatarTooltip>
                  ))}
                </div>
                <div className="flex space-x-3 md:space-x-4 ">
                  {restAvatarPartsPickers.map((picker) => (
                    <AvatarTooltip
                      key={picker.path}
                      text={picker.text}
                      width={picker.width}
                    >
                      <div className="flex items-center overflow-x-auto">
                        {picker.part !== "bg" ? (
                          <AvatarPartPicker
                            path={picker.path}
                            onClick={() => openAvatarModalPicker(picker)}
                          />
                        ) : (
                          <AvatarBackgroundPicker
                            color={avatar.bg}
                            onClick={() => openAvatarBackgroundModal()}
                          />
                        )}
                      </div>
                    </AvatarTooltip>
                  ))}
                </div>
              </div>
            </div>
            <div className="flex flex-col items-center justify-center">
              <div className="flex space-x-2 md:space-x-4 pt-6">
                <AvatarTooltip text="Download" width={60}>
                  <AvatarPartPicker
                    path="base/Download"
                    onClick={() => openAvatarDownloadOptionModal()}
                  />
                </AvatarTooltip>
                <AvatarTooltip text="Randomize" width={60}>
                  <AvatarPartPicker
                    path="base/Reload"
                    onClick={() => handleRandomizeAvatar()}
                  />
                </AvatarTooltip>
                <AvatarTooltip text="Share" width={60}>
                  <AvatarPartPicker path="base/Share" onClick={() => share()} />
                </AvatarTooltip>
                <AvatarTooltip text="Sound" width={60}>
                  <AvatarPartPicker
                    path={`base/${soundEnabled ? "SoundLoud" : "SoundOff"}`}
                    onClick={() => toggleSound(!soundEnabled)}
                  />
                </AvatarTooltip>
                <button
                  type="button"
                  onClick={useAvatarInProfile}
                  className="rounded-full bg-teal-500 px-3 py-2 text-xs font-semibold text-white shadow transition hover:bg-teal-600"
                >
                  Dùng avatar
                </button>
              </div>
            </div>
            {!isEmbedded && <FAQs />}
            {!isEmbedded && (
              <div className="pb-24">
                <Footer />
              </div>
            )}
          </div>
        )}
      </div>
      <AvatarPartModal
        {...avatarModal}
        isOpen={isAvatarModalPickerOpen}
        onPartSelected={(part, src) => closeAvatarModalPicker(part, src)}
        onClose={() => setIsAvatarModalPickerOpen(false)}
      />
      <AvatarBackgroundModal
        isOpen={isBackgroundModalOpen}
        backgrounds={backgrounds}
        activeBackground={avatar.bg}
        onBackgroundSelected={(bg) =>
          setAvatar((prev) => ({
            ...prev,
            bg,
          }))
        }
        onClose={() => setIsBackgroundModalOpen(false)}
      />
      <AvatarDownloadOptionModal
        isOpen={isDownloadOptionModalOpen}
        onDownloadOption={(option: "SVG" | "PNG") =>
          option === "SVG"
            ? handleDownloadAvatarSVG()
            : handleDownloadAvatarPNG()
        }
        onClose={() => setIsDownloadOptionModalOpen(false)}
      />
      <Toaster />
    </>
  );
}

export default App;
