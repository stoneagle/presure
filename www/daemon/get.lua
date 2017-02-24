function rnd(max)
  local ret = 0
  math.randomseed(os.time())
  for i = 1,3 do
    n = math.random(max)
    ret = n
  end
  return ret
end

-- function init(args)
--     msg = "thread %d created" 
--     print(msg:format(id))
-- end
--延迟部分
function delay()
    --不能跟delay重名
    return rnd(50)
end

request = function()
  --wrk.method = "GET"
  wrk.method = "POST"
  wrk.body  = "name=xxx&pw=123456"  
  wrk.headers["Content-Type"] = "application/x-www-form-urlencoded"
  return wrk.format(nil,path)
end

done = function(summary,latency,request)
  io.write("---------------\n")
  for _,p in pairs({50,60,90,95,99,99.99}) do
    n = latency:percentile(p)
    io.write(string.format("%g%%,%d ms\n",p,n/1000.0))
  end
end

