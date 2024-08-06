import "./styles.css"
import {Image} from "@telegram-apps/telegram-ui";

export function DirectionCard({direction, pathImg, onClick}) {
    return (
      <div onClick={onClick} style={{cursor: 'pointers'}}>
          <div className="container-card">
              <Image
                  className={"border-radius-icon"}
                  size={40}
                  src={`${pathImg}`}
                  alt={'logo'}
              />

              <div className="text-container">
                  <h1 className={"title-card"}>{direction}</h1>
                  <h4 className={"desc-card"}>Информация, камеры, правила и др.</h4>
              </div>
          </div>
      </div>
    );
}
