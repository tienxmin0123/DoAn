import React from "react";
import "./Footer.css";
function Footer(props) {
  return (
    <>
      <div id="footer">
        <div className="container" style={{ display: "flex" }}>
          <div id="user1" className="col-sm-4 col-xs-12">
            <div className="moduletable">
              <h3>TVSTORE UY TÍN - CHẤT LƯỢNG</h3>
              <div className="custom">
                <h3 className="title-footer-2">
                  Email: cskh@tvstorevn.com
                  <br />
                </h3>
                <p style={{ fontSize: "14px" }}>
                  <b>Tổng đài miễn phí</b>
                  (Làm việc từ 8h00 - 22h00)
                </p>
                <table style={{ width: "296px" }}>
                  <tbody>
                    <tr>
                      <td style={{ width: "211px" }}>Gọi mua hàng</td>
                      <td style={{ width: "105px" }}>
                        <strong>02438370598</strong>
                      </td>
                    </tr>
                    <tr>
                      <td style={{ width: "211px" }}>Hỗ trợ khách hàng </td>
                      <td style={{ width: "105px" }}>
                        <strong>0961743857</strong>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div id="user2" className="col-sm-5 col-xs-12">
            <b>Hệ Thống Cửa Hàng</b>
            <p>
              <b>SHOWROOM HÀ NỘI </b>
              (Làm việc từ 8h00 - 21h00)
              <br /> - Địa chỉ 1: 41A Đ. Phú Diễn, Phú Diễn, Bắc Từ Liêm, Hà Nội.
            </p>
            <p>
              <b>SHOWROOM THANH HÓA </b>
              (Làm việc từ 8h00 - 21h00)
              <br />- Địa chỉ : Số 4, Trần Phú, P. Ba Đình, Bỉm Sơn, Thanh Hoá.
            </p>
          </div>
        </div>
      </div>
    </>
  );
}

export default React.memo(Footer) ;