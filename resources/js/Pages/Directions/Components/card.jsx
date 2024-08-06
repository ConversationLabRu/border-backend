import {Image} from "@telegram-apps/telegram-ui";
import "./styles.css"

export function DirectionCard({direction, pathImg, onClick}) {
    return (
      <div onClick={onClick} style={{cursor: 'pointers'}}>
          <div className="container-card">
              <Image
                  className={"border-radius-icon"}
                  size={40}
                  src={`${pathImg}`}
              />

              <div className="text-container">
                  <h1 className={"title-card"}>{direction}</h1>
                  <h4 className={"desc-card"}>Информация, камеры, правила и др.</h4>
              </div>
          </div>
      </div>
    );
}
